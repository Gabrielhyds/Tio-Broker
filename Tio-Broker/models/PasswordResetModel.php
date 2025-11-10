<?php

class PasswordResetModel
{
    /**
     * @var mysqli A conexão com o banco de dados.
     */
    private $conn;

    /**
     * Construtor da classe.
     * @param mysqli $conn A instância da conexão com o banco de dados.
     */
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Cria um novo token de redefinição de senha no banco de dados.
     */
    public function createToken($user_id, $token)
    {
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->conn->prepare(
            "INSERT INTO password_resets (user_id, token, expires_at, used) VALUES (?, ?, ?, FALSE)"
        );

        // --- CORREÇÃO ---
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("iss", $user_id, $token, $expiresAt);
        return $stmt->execute();
    }

    /**
     * Busca e valida um token no banco de dados.
     */
    public function getToken($token)
    {
        $sql = "SELECT * FROM password_resets WHERE token = ? AND used = FALSE AND expires_at > NOW()";
        $stmt = $this->conn->prepare($sql);

        // --- CORREÇÃO ---
        if ($stmt === false) {
            return null;
        }

        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result ? $result->fetch_assoc() : null;
    }

    /**
     * Invalida um token marcando-o como usado no banco de dados.
     */
    public function invalidateToken($token)
    {
        $stmt = $this->conn->prepare("UPDATE password_resets SET used = TRUE WHERE token = ?");

        // --- CORREÇÃO ---
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("s", $token);
        return $stmt->execute();
    }
}
