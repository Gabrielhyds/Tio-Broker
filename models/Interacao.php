<?php

class Interacao
{
    private $db;

    /**
     * Construtor da classe, que recebe a conexão ao ser instanciada.
     * @param mysqli $db A instância da conexão com o banco de dados.
     */
    public function __construct($db)
    {
        // CORREÇÃO: Adiciona uma verificação para garantir que a conexão é válida.
        if ($db instanceof mysqli) {
            $this->db = $db;
        } else {
            // Se a conexão for inválida, lança uma exceção para interromper a execução.
            throw new Exception("Conexão com banco de dados inválida fornecida para InteracaoModel.");
        }
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
            "iiss",
            $dados['id_cliente'],
            $dados['id_usuario'],
            $dados['tipo_interacao'],
            $dados['descricao']
        );

        $success = $stmt->execute();
        if (!$success) {
            error_log("Falha na execução (cadastrar interação): (" . $stmt->errno . ") " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }

    /**
     * Lista todas as interações de um cliente específico.
     * @param int $idCliente ID do cliente.
     * @return array Lista de interações.
     */
    public function listarPorCliente($idCliente)
    {
        $interacoes = [];
        $sql = "SELECT i.*, u.nome as nome_usuario 
                FROM interacoes i
                JOIN usuario u ON i.id_usuario = u.id_usuario
                WHERE i.id_cliente = ? 
                ORDER BY i.data_interacao DESC";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            error_log("Falha no prepare (listar interações): (" . $this->db->errno . ") " . $this->db->error);
            return $interacoes;
        }

        $stmt->bind_param("i", $idCliente);

        if (!$stmt->execute()) {
            error_log("Falha na execução (listar interações): (" . $stmt->errno . ") " . $stmt->error);
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
}
