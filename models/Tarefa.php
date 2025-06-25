<?php
class Tarefa
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    public function listar()
    {
        $sql = "SELECT t.*, u.nome as nome_usuario, c.nome as nome_cliente
                FROM tarefas t
                LEFT JOIN usuario u ON t.id_usuario = u.id_usuario
                LEFT JOIN cliente c ON t.id_cliente = c.id_cliente
                ORDER BY t.data_criacao DESC";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tarefas WHERE id_tarefa = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function criar($id_usuario, $id_cliente, $descricao, $status, $prioridade, $prazo)
    {
        $stmt = $this->conn->prepare("INSERT INTO tarefas (id_usuario, id_cliente, descricao, status, prioridade, prazo) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $id_usuario, $id_cliente, $descricao, $status, $prioridade, $prazo);
        return $stmt->execute();
    }

    public function atualizar($id_tarefa, $id_usuario, $id_cliente, $descricao, $status, $prioridade, $prazo)
    {
        $stmt = $this->conn->prepare("UPDATE tarefas SET id_usuario=?, id_cliente=?, descricao=?, status=?, prioridade=?, prazo=? WHERE id_tarefa=?");
        $stmt->bind_param("iissssi", $id_usuario, $id_cliente, $descricao, $status, $prioridade, $prazo, $id_tarefa);
        return $stmt->execute();
    }

    public function excluir($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM tarefas WHERE id_tarefa = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
