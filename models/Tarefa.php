<?php

class Tarefa
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function listarPorUsuario($id_usuario)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tarefas WHERE id_usuario = ? ORDER BY data_criacao DESC");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function buscarPorId($id_tarefa)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tarefas WHERE id_tarefa = ?");
        $stmt->bind_param("i", $id_tarefa);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function cadastrar($id_usuario, $id_cliente, $descricao, $status)
    {
        $sql = "INSERT INTO tarefas (id_usuario, id_cliente, descricao, status) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            echo "Erro no prepare: " . $this->conn->error;
            return false;
        }

        $stmt->bind_param("iiss", $id_usuario, $id_cliente, $descricao, $status);

        if (!$stmt->execute()) {
            echo "Erro no execute: " . $stmt->error;
            return false;
        }

        return true;
    }

    public function atualizar($id_tarefa, $descricao, $status, $data_conclusao = null)
    {
        $query = "UPDATE tarefas SET descricao = ?, status = ?, data_conclusao = ? WHERE id_tarefa = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssi", $descricao, $status, $data_conclusao, $id_tarefa);
        return $stmt->execute();
    }

    public function excluir($id_tarefa)
    {
        $stmt = $this->conn->prepare("DELETE FROM tarefas WHERE id_tarefa = ?");
        $stmt->bind_param("i", $id_tarefa);
        return $stmt->execute();
    }
}
