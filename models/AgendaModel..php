<?php

if (!class_exists('AgendaModel')) {
    
    class AgendaModel {
        private $pdo;

        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        /**
         * Busca todos os eventos de um usuário, garantindo que todos sejam retornados.
         * @param int $id_usuario O ID do usuário para buscar os eventos.
         * @return array Retorna um array com os eventos encontrados.
         */
        public function buscarEventosPorUsuario($id_usuario) {
            // CORRIGIDO: Trocado para LEFT JOIN para garantir que o evento apareça
            // mesmo que o id_cliente seja inválido ou não exista.
            $sql = "SELECT 
                        e.id_evento,
                        e.titulo,
                        e.data_inicio,
                        e.data_fim,
                        e.tipo_evento,
                        COALESCE(c.nome, 'Cliente não encontrado') as nome_cliente 
                    FROM agenda_eventos e
                    LEFT JOIN cliente c ON e.id_cliente = c.id_cliente
                    WHERE e.id_usuario = :id_usuario";
            
            try {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([':id_usuario' => $id_usuario]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erro ao buscar eventos: " . $e->getMessage());
                return []; 
            }
        }

        /**
         * Cria um novo evento na agenda.
         * @param array $dados Os dados do evento a ser criado.
         * @return bool Retorna true em caso de sucesso, false em caso de falha.
         */
        public function criarEvento($dados) {
            $sql = "INSERT INTO agenda_eventos (id_usuario, id_cliente, titulo, descricao, data_inicio, data_fim, tipo_evento, lembrete) 
                    VALUES (:id_usuario, :id_cliente, :titulo, :descricao, :data_inicio, :data_fim, :tipo_evento, :lembrete)";
            
            try {
                $stmt = $this->pdo->prepare($sql);
                
                return $stmt->execute([
                    ':id_usuario'   => $dados['id_usuario'],
                    ':id_cliente'   => $dados['id_cliente'],
                    ':titulo'       => $dados['titulo'],
                    ':descricao'    => $dados['descricao'],
                    ':data_inicio'  => $dados['data_inicio'],
                    ':data_fim'     => $dados['data_fim'],
                    ':tipo_evento'  => $dados['tipo_evento'],
                    ':lembrete'     => $dados['lembrete'] ?? 0
                ]);
            } catch (PDOException $e) {
                // Loga o erro específico (ex: falha de chave estrangeira)
                error_log('Erro ao criar evento: ' . $e->getMessage());
                return false;
            }
        }
    }
}
