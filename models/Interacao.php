<?php

class Interacao
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Cadastra uma nova interação no banco de dados.
     * @param array $dados Dados da interação (id_cliente, id_usuario, tipo_interacao, descricao).
     * @return bool True em sucesso, false em falha.
     */
    public function cadastrar($dados)
    {
        $sql = "INSERT INTO interacoes (id_cliente, id_usuario, tipo_interacao, descricao, data_interacao) 
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            error_log("Falha no prepare (cadastrar interação): (" . $this->db->errno . ") " . $this->db->error);
            return false;
        }

        $stmt->bind_param(
            "iiss", // i: integer, s: string
            $dados['id_cliente'],
            $dados['id_usuario'],
            $dados['tipo_interacao'],
            $dados['descricao']
        );

        $success = $stmt->execute();
        if (!$success) {
            error_log("Falha na execução (cadastrar interação): (" . $stmt->errno . ") " . $stmt->error . " SQL: " . $sql);
        }
        $stmt->close();
        return $success;
    }

    /**
     * Lista todas as interações de um cliente específico, com informações do usuário que registrou.
     * @param int $idCliente ID do cliente.
     * @return array Lista de interações.
     */
    public function listarPorCliente($idCliente)
    {
        $interacoes = [];
        // Adicionado JOIN com a tabela 'usuario' para pegar o nome do usuário que fez a interação
        $sql = "SELECT i.*, u.nome as nome_usuario 
                FROM interacoes i
                JOIN usuario u ON i.id_usuario = u.id_usuario
                WHERE i.id_cliente = ? 
                ORDER BY i.data_interacao DESC"; // Mais recentes primeiro
        
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            error_log("Falha no prepare (listar interações por cliente): (" . $this->db->errno . ") " . $this->db->error . " SQL: " . $sql);
            return $interacoes;
        }

        $stmt->bind_param("i", $idCliente);

        if(!$stmt->execute()){
            error_log("Falha na execução (listar interações por cliente): (" . $stmt->errno . ") " . $stmt->error);
            $stmt->close();
            return $interacoes;
        }
        
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $interacoes[] = $row;
        }
        $stmt->close();
        return $interacoes;
    }

    // Futuramente, métodos para excluir ou editar interações podem ser adicionados aqui.
}
