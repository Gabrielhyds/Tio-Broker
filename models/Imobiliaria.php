<?php
class Imobiliaria {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function cadastrar($nome, $cnpj) {
        $stmt = $this->conn->prepare("INSERT INTO imobiliaria (nome, cnpj) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome, $cnpj);
        return $stmt->execute();
    }

    public function listarTodas() {
        $query = "
            SELECT i.*, COUNT(u.id_usuario) as total_usuarios
            FROM imobiliaria i
            LEFT JOIN usuario u ON i.id_imobiliaria = u.id_imobiliaria
            GROUP BY i.id_imobiliaria
            ORDER BY i.nome ASC
        ";
        $resultado = $this->conn->query($query);
        if (!$resultado) return [];
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function excluir($id) {
        $stmt = $this->conn->prepare("DELETE FROM imobiliaria WHERE id_imobiliaria = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function buscarPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM imobiliaria WHERE id_imobiliaria = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function atualizar($id, $nome, $cnpj) {
        $stmt = $this->conn->prepare("UPDATE imobiliaria SET nome = ?, cnpj = ? WHERE id_imobiliaria = ?");
        $stmt->bind_param("ssi", $nome, $cnpj, $id);
        return $stmt->execute();
    }
}
?>