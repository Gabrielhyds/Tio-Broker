<?php
/**
 * Model da Agenda
 * Responsável por toda a comunicação com a tabela 'agenda_eventos' no banco de dados.
 */

if (!class_exists('AgendaModel')) {
    class AgendaModel
    {
        private $connection;

        public function __construct($connection)
        {
            $this->connection = $connection;
        }

        /**
         * Busca todos os eventos de um usuário.
         */
        public function buscarEventosPorUsuario($id_usuario)
        {
            $sql = "SELECT 
                        e.id_evento, e.titulo, e.descricao, e.data_inicio, e.data_fim, e.tipo_evento, e.lembrete,
                        e.id_cliente, e.id_imovel, e.feedback,
                        COALESCE(c.nome, 'Sem cliente') as nome_cliente 
                    FROM agenda_eventos e
                    LEFT JOIN cliente c ON e.id_cliente = c.id_cliente
                    WHERE e.id_usuario = ?";
            $stmt = $this->connection->prepare($sql);
            if ($stmt === false) return [];
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        /**
         * Busca um evento específico pelo seu ID.
         */
        public function buscarEventoPorId($id_evento, $id_usuario)
        {
            $sql = "SELECT * FROM agenda_eventos WHERE id_evento = ? AND id_usuario = ?";
            $stmt = $this->connection->prepare($sql);
            if ($stmt === false) return null;
            $stmt->bind_param("ii", $id_evento, $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result->fetch_assoc();
        }

        /**
         * Cria um novo evento no banco de dados.
         */
        public function criarEvento($dados)
        {
            $sql = "INSERT INTO agenda_eventos (id_usuario, id_cliente, id_imovel, titulo, descricao, data_inicio, data_fim, tipo_evento, lembrete, feedback) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($sql);
            if ($stmt === false) return false;

            // Os campos 'id_cliente' e 'id_imovel' são tratados como string ('s') para aceitar valores NULOS.
            $stmt->bind_param( "isssssssis",
                $dados['id_usuario'],
                $dados['id_cliente'],
                $dados['id_imovel'],
                $dados['titulo'],
                $dados['descricao'],
                $dados['data_inicio'],
                $dados['data_fim'],
                $dados['tipo_evento'],
                $dados['lembrete'],
                $dados['feedback']
            );
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }

        /**
         * Atualiza um evento existente.
         */
        public function atualizarEvento($id_evento, $dados)
        {
            $sql = "UPDATE agenda_eventos SET 
                        id_cliente = ?, id_imovel = ?, titulo = ?, descricao = ?, data_inicio = ?, 
                        data_fim = ?, tipo_evento = ?, lembrete = ?, feedback = ?
                    WHERE id_evento = ? AND id_usuario = ?";
            $stmt = $this->connection->prepare($sql);
            if ($stmt === false) return false;
            
            /**
             * (CORRIGIDO) A string de tipos estava errada. Faltava um 's' para o campo 'tipo_evento'.
             * O número de caracteres na string de tipos deve corresponder exatamente ao número de variáveis.
             * Tipos antigos (errado): 'ssssssisii' (10 caracteres para 11 variáveis)
             * Tipos novos (correto): 'sssssssisii' (11 caracteres para 11 variáveis)
             */
            $stmt->bind_param("sssssssisii", // <-- CORREÇÃO APLICADA AQUI
                $dados['id_cliente'],
                $dados['id_imovel'],
                $dados['titulo'],
                $dados['descricao'],
                $dados['data_inicio'],
                $dados['data_fim'],
                $dados['tipo_evento'],
                $dados['lembrete'],
                $dados['feedback'],
                $id_evento,
                $dados['id_usuario']
            );
            $success = $stmt->execute();
            if (!$success) {
                // Adiciona um log de erro para facilitar a depuração futura.
                error_log('Erro ao executar a atualização do evento: ' . $stmt->error);
            }
            $stmt->close();
            return $success;
        }

        /**
         * Atualiza apenas a data de um evento (usado no drag-and-drop).
         */
        public function atualizarDataEvento($id_evento, $data_inicio, $data_fim, $id_usuario)
        {
            $sql = "UPDATE agenda_eventos SET data_inicio = ?, data_fim = ? WHERE id_evento = ? AND id_usuario = ?";
            $stmt = $this->connection->prepare($sql);
            if ($stmt === false) return false;
            $stmt->bind_param("ssii", $data_inicio, $data_fim, $id_evento, $id_usuario);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }

        /**
         * Exclui um evento do banco de dados.
         */
        public function excluirEvento($id_evento, $id_usuario)
        {
            $sql = "DELETE FROM agenda_eventos WHERE id_evento = ? AND id_usuario = ?";
            $stmt = $this->connection->prepare($sql);
            if ($stmt === false) return false;
            $stmt->bind_param("ii", $id_evento, $id_usuario);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
    }
}
