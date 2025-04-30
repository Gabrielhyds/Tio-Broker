<?php
class Usuario {
    private $conn;

    // Construtor recebe a conexão do banco
    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Cadastra um novo usuário no sistema
     */
    public function cadastrar($nome, $email, $cpf, $telefone, $senha, $permissao) {
        // Criptografa a senha usando md5
        $senha_hash = md5($senha);

        // Prepara a consulta SQL com segurança (evita SQL Injection)
        $stmt = $this->conn->prepare("
            INSERT INTO usuario (nome, email, cpf, telefone, senha, permissao)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            die("Erro na preparação da query: " . $this->conn->error);
        }

        $stmt->bind_param("ssssss", $nome, $email, $cpf, $telefone, $senha_hash, $permissao);

        // Executa e retorna sucesso ou erro
        return $stmt->execute();
    }

    /**
     * Realiza login com email e senha
     */
    public function login($email, $senha) {
        // Prepara a consulta SQL
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE email = ?");

        if (!$stmt) {
            die("Erro ao preparar login: " . $this->conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        // Verifica se o usuário existe
        if ($resultado->num_rows === 0) {
            return false; // Email não encontrado
        }

        $usuario = $resultado->fetch_assoc();

        // Compara a senha md5
        if (md5($senha) === $usuario['senha']) {
            return $usuario; // Login OK
        }

        return false; // Senha incorreta
    }
}
?>
