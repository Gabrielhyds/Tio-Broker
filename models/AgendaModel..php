<?php

if (!class_exists('AgendaModel')) {
    
    class AgendaModel {
        private $connection;

        public function __construct($connection) {
            $this->connection = $connection;
        }

        /**
         * Busca todos os eventos de um usuário usando mysqli.
         * @param int $id_usuario O ID do usuário.
         * @return array Retorna um array com os eventos.
         */
        public function buscarEventosPorUsuario($id_usuario) {
            $sql = "SELECT 
                        e.id_evento, e.titulo, e.data_inicio, e.data_fim, e.tipo_evento, 
                        COALESCE(c.nome, 'Sem cliente') as nome_cliente 
                    FROM agenda_eventos e
                    LEFT JOIN cliente c ON e.id_cliente = c.id_cliente
                    WHERE e.id_usuario = ?";
            
            $stmt = $this->connection->prepare($sql);
            if ($stmt === false) { return []; }

            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            return $result->fetch_all(MYSQLI_ASSOC);
        }

        /**
         * Cria um novo evento na agenda usando mysqli.
         * @param array $dados Os dados do evento.
         * @return bool Retorna true em caso de sucesso.
         */
        public function criarEvento($dados) {
            $sql = "INSERT INTO agenda_eventos (id_usuario, id_cliente, titulo, descricao, data_inicio, data_fim, tipo_evento, lembrete) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->connection->prepare($sql);
            if ($stmt === false) {
                error_log("Erro de preparação no SQL: " . $this->connection->error);
                return false;
            }

            // O tipo 'i' para um valor null será convertido para 0, o que é aceitável
            // pois a coluna id_cliente agora permite nulos.
            $stmt->bind_param(
                "iisssssi",
                $dados['id_usuario'],
                $dados['id_cliente'],
                $dados['titulo'],
                $dados['descricao'],
                $dados['data_inicio'],
                $dados['data_fim'],
                $dados['tipo_evento'],
                $dados['lembrete']
            );
            
            $success = $stmt->execute();
            if (!$success) {
                error_log("Erro de execução do evento: " . $stmt->error);
            }
            $stmt->close();

            return $success;
        }
    }
}
