<?php

// Verifica se a classe AgendaModel ainda não foi definida para evitar erros de redeclaração.
if (!class_exists('AgendaModel')) {

    // Define a classe AgendaModel, responsável pela lógica de negócios da agenda.
    class AgendaModel
    {
        // Propriedade privada para armazenar o objeto de conexão com o banco de dados.
        private $connection;

        // Construtor da classe, chamado quando um objeto AgendaModel é criado.
        public function __construct($connection)
        {
            // Atribui a conexão recebida à propriedade da classe.
            $this->connection = $connection;
        }

        /**
         * Busca todos os eventos de um usuário específico.
         * A consulta foi atualizada para trazer todos os dados necessários para a edição.
         * @param int $id_usuario O ID do usuário cujos eventos serão buscados.
         * @return array Retorna um array associativo com os eventos encontrados.
         */
        public function buscarEventosPorUsuario($id_usuario)
        {
            $sql = "SELECT 
                        e.id_evento, e.titulo, e.descricao, e.data_inicio, e.data_fim, e.tipo_evento, e.lembrete,
                        e.id_cliente,
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
         * (NOVO) Busca um único evento pelo seu ID, garantindo que ele pertence ao usuário.
         * Essencial para a funcionalidade de edição.
         * @param int $id_evento O ID do evento a ser buscado.
         * @param int $id_usuario O ID do usuário para verificação de permissão.
         * @return array|null Retorna o evento ou nulo se não encontrado.
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
         * Cria um novo evento na agenda.
         * @param array $dados Os dados do evento a ser inserido.
         * @return bool Retorna true se o evento foi criado com sucesso, false caso contrário.
         */
        public function criarEvento($dados)
        {
            $sql = "INSERT INTO agenda_eventos (id_usuario, id_cliente, titulo, descricao, data_inicio, data_fim, tipo_evento, lembrete) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->connection->prepare($sql);
            if ($stmt === false) {
                error_log("Erro de preparação no SQL (criarEvento): " . $this->connection->error);
                return false;
            }

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
                error_log("Erro de execução do evento (criarEvento): " . $stmt->error);
            }
            $stmt->close();
            return $success;
        }

        /**
         * (NOVO) Atualiza um evento existente no banco de dados.
         * @param int $id_evento O ID do evento a ser atualizado.
         * @param array $dados Os novos dados do evento.
         * @return bool Retorna true se a atualização foi bem-sucedida.
         */
        public function atualizarEvento($id_evento, $dados)
        {
            $sql = "UPDATE agenda_eventos SET 
                        id_cliente = ?, titulo = ?, descricao = ?, data_inicio = ?, 
                        data_fim = ?, tipo_evento = ?, lembrete = ?
                    WHERE id_evento = ? AND id_usuario = ?";

            $stmt = $this->connection->prepare($sql);
            if ($stmt === false) {
                error_log("Erro de preparação no SQL (atualizarEvento): " . $this->connection->error);
                return false;
            }
            
            $stmt->bind_param(
                "isssssiii",
                $dados['id_cliente'],
                $dados['titulo'],
                $dados['descricao'],
                $dados['data_inicio'],
                $dados['data_fim'],
                $dados['tipo_evento'],
                $dados['lembrete'],
                $id_evento,
                $dados['id_usuario']
            );
            
            $success = $stmt->execute();
             if (!$success) {
                error_log("Erro de execução do evento (atualizarEvento): " . $stmt->error);
            }
            $stmt->close();
            return $success;
        }

        /**
         * (NOVO) Atualiza apenas a data de início e fim de um evento (para drag & drop).
         * @param int $id_evento O ID do evento.
         * @param string $data_inicio A nova data de início.
         * @param string $data_fim A nova data de fim.
         * @param int $id_usuario O ID do usuário para segurança.
         * @return bool Retorna true se a atualização foi bem-sucedida.
         */
        public function atualizarDataEvento($id_evento, $data_inicio, $data_fim, $id_usuario)
        {
            $sql = "UPDATE agenda_eventos SET data_inicio = ?, data_fim = ? 
                    WHERE id_evento = ? AND id_usuario = ?";

            $stmt = $this->connection->prepare($sql);
            if ($stmt === false) return false;

            $stmt->bind_param("ssii", $data_inicio, $data_fim, $id_evento, $id_usuario);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }

        /**
         * (NOVO) Exclui um evento do banco de dados.
         * @param int $id_evento O ID do evento a ser excluído.
         * @param int $id_usuario O ID do usuário para segurança.
         * @return bool Retorna true se a exclusão foi bem-sucedida.
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
