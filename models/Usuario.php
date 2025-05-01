<?php
class Usuario {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function cadastrar($nome, $email, $cpf, $telefone, $senha, $permissao, $id_imobiliaria, $creci = null, $foto = null) {
        $senha_hash = md5($senha);
        $stmt = $this->conn->prepare("INSERT INTO usuario (nome, email, cpf, telefone, senha, permissao, id_imobiliaria, creci, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssiss", $nome, $email, $cpf, $telefone, $senha_hash, $permissao, $id_imobiliaria, $creci, $foto);
        return $stmt->execute();
    }

    public function login($email, $senha) {
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE email = ?");

        if (!$stmt) {
            die("Erro ao preparar login: " . $this->conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {
            return false;
        }

        $usuario = $resultado->fetch_assoc();

        if (md5($senha) === $usuario['senha']) {
            return $usuario;
        }

        return false;
    }

    public function listarTodosComImobiliaria() {
        $query = "
            SELECT u.*, i.nome AS nome_imobiliaria
            FROM usuario u
            LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria
            ORDER BY u.nome ASC
        ";
        $resultado = $this->conn->query($query);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function buscarPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function atualizar($id, $nome, $email, $cpf, $telefone, $permissao, $id_imobiliaria, $creci = null, $foto = null) {
        $stmt = $this->conn->prepare("UPDATE usuario SET nome = ?, email = ?, cpf = ?, telefone = ?, permissao = ?, id_imobiliaria = ?, creci = ?, foto = ? WHERE id_usuario = ?");
        $stmt->bind_param("ssssssssi", $nome, $email, $cpf, $telefone, $permissao, $id_imobiliaria, $creci, $foto, $id);
        return $stmt->execute();
    }

    public function excluir($id) {
        $stmt = $this->conn->prepare("DELETE FROM usuario WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>