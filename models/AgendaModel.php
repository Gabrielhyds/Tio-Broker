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
         * Busca todos os eventos de um usuário específico usando mysqli.
         * @param int $id_usuario O ID do usuário cujos eventos serão buscados.
         * @return array Retorna um array associativo com os eventos encontrados.
         */
        public function buscarEventosPorUsuario($id_usuario)
        {
            // Define a consulta SQL para selecionar eventos, juntando com a tabela de clientes.
            $sql = "SELECT 
                        e.id_evento, e.titulo, e.data_inicio, e.data_fim, e.tipo_evento, 
                        COALESCE(c.nome, 'Sem cliente') as nome_cliente 
                    FROM agenda_eventos e
                    LEFT JOIN cliente c ON e.id_cliente = c.id_cliente
                    WHERE e.id_usuario = ?";

            // Prepara a consulta SQL para execução.
            $stmt = $this->connection->prepare($sql);
            // Se a preparação falhar, retorna um array vazio para evitar erros.
            if ($stmt === false) {
                return [];
            }

            // Associa o parâmetro (ID do usuário) à consulta preparada. 'i' significa que é um inteiro.
            $stmt->bind_param("i", $id_usuario);
            // Executa a consulta.
            $stmt->execute();
            // Obtém o conjunto de resultados da consulta.
            $result = $stmt->get_result();
            // Fecha o statement para liberar recursos.
            $stmt->close();

            // Retorna todos os resultados como um array de arrays associativos.
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        /**
         * Cria um novo evento na agenda usando mysqli.
         * @param array $dados Os dados do evento a ser inserido.
         * @return bool Retorna true se o evento foi criado com sucesso, false caso contrário.
         */
        public function criarEvento($dados)
        {
            // Define a consulta SQL para inserir um novo evento na tabela.
            $sql = "INSERT INTO agenda_eventos (id_usuario, id_cliente, titulo, descricao, data_inicio, data_fim, tipo_evento, lembrete) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            // Prepara a consulta SQL para execução.
            $stmt = $this->connection->prepare($sql);
            // Se a preparação da consulta falhar.
            if ($stmt === false) {
                // Registra o erro no log do servidor para depuração.
                error_log("Erro de preparação no SQL: " . $this->connection->error);
                // Retorna false para indicar que a operação falhou.
                return false;
            }

            // Associa os parâmetros do array $dados à consulta preparada.
            // A string "iisssssi" especifica os tipos de dados de cada parâmetro (integer, integer, string, string, etc.).
            $stmt->bind_param(
                "iisssssi",
                $dados['id_usuario'],
                $dados['id_cliente'], // Se for nulo, será tratado como 0, o que é aceitável pela coluna.
                $dados['titulo'],
                $dados['descricao'],
                $dados['data_inicio'],
                $dados['data_fim'],
                $dados['tipo_evento'],
                $dados['lembrete']
            );

            // Executa a instrução de inserção.
            $success = $stmt->execute();
            // Se a execução falhar.
            if (!$success) {
                // Registra o erro específico da execução no log.
                error_log("Erro de execução do evento: " . $stmt->error);
            }
            // Fecha o statement para liberar recursos do servidor.
            $stmt->close();

            // Retorna o status de sucesso (true ou false) da execução.
            return $success;
        }
    }
}
