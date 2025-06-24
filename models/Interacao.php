<?php

class Interacao
{
    // Propriedade privada para armazenar a conexão com o banco de dados.
    private $db;

    // Construtor da classe, que recebe a conexão ao ser instanciada.
    public function __construct($db)
    {
        // Atribui a conexão recebida à propriedade da classe.
        $this->db = $db;
    }

    /**
     * Cadastra uma nova interação no banco de dados.
     * @param array $dados Dados da interação (id_cliente, id_usuario, tipo_interacao, descricao).
     * @return bool True em sucesso, false em falha.
     */
    public function cadastrar($dados)
    {
        // Define a instrução SQL para inserir uma nova interação. `NOW()` insere a data e hora atuais.
        $sql = "INSERT INTO interacoes (id_cliente, id_usuario, tipo_interacao, descricao, data_interacao) 
                VALUES (?, ?, ?, ?, NOW())";
        // Prepara a consulta SQL para execução, prevenindo injeção de SQL.
        $stmt = $this->db->prepare($sql);

        // Se a preparação da consulta falhar, registra um erro e retorna falso.
        if (!$stmt) {
            error_log("Falha no prepare (cadastrar interação): (" . $this->db->errno . ") " . $this->db->error);
            return false;
        }

        // Associa (bind) os valores do array $dados aos placeholders (?) na consulta.
        $stmt->bind_param(
            "iiss", // i: integer, s: string
            $dados['id_cliente'],
            $dados['id_usuario'],
            $dados['tipo_interacao'],
            $dados['descricao']
        );

        // Executa a instrução preparada.
        $success = $stmt->execute();
        // Se a execução falhar, registra um erro detalhado para depuração.
        if (!$success) {
            error_log("Falha na execução (cadastrar interação): (" . $stmt->errno . ") " . $stmt->error . " SQL: " . $sql);
        }
        // Fecha o statement para liberar recursos.
        $stmt->close();
        // Retorna true em caso de sucesso ou false em caso de falha.
        return $success;
    }

    /**
     * Lista todas as interações de um cliente específico, com informações do usuário que registrou.
     * @param int $idCliente ID do cliente.
     * @return array Lista de interações.
     */
    public function listarPorCliente($idCliente)
    {
        // Inicializa um array vazio para armazenar as interações.
        $interacoes = [];
        // Define a consulta SQL para selecionar interações, juntando com a tabela 'usuario' para obter o nome do usuário.
        $sql = "SELECT i.*, u.nome as nome_usuario 
                FROM interacoes i
                JOIN usuario u ON i.id_usuario = u.id_usuario
                WHERE i.id_cliente = ? 
                ORDER BY i.data_interacao DESC"; // Ordena para mostrar as interações mais recentes primeiro.

        // Prepara a consulta.
        $stmt = $this->db->prepare($sql);

        // Se a preparação falhar, registra o erro e retorna um array vazio.
        if (!$stmt) {
            error_log("Falha no prepare (listar interações por cliente): (" . $this->db->errno . ") " . $this->db->error . " SQL: " . $sql);
            return $interacoes;
        }

        // Associa o ID do cliente ao placeholder da consulta.
        $stmt->bind_param("i", $idCliente);

        // Executa a consulta.
        if (!$stmt->execute()) {
            // Se a execução falhar, registra o erro.
            error_log("Falha na execução (listar interações por cliente): (" . $stmt->errno . ") " . $stmt->error);
            // Fecha o statement e retorna o array vazio.
            $stmt->close();
            return $interacoes;
        }

        // Obtém o conjunto de resultados da consulta.
        $result = $stmt->get_result();

        // Itera sobre cada linha do resultado e a adiciona ao array de interações.
        while ($row = $result->fetch_assoc()) {
            $interacoes[] = $row;
        }
        // Fecha o statement.
        $stmt->close();
        // Retorna o array com as interações encontradas.
        return $interacoes;
    }

    // Futuramente, métodos para excluir ou editar interações podem ser adicionados aqui.
}
