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
     * @param int $user_id O ID do usuário.
     * @param string $token O token seguro gerado.
     * @return bool Retorna true em caso de sucesso, false em caso de falha.
     */
    public function createToken($user_id, $token)
    {
        // Define a data de expiração para 1 hora a partir de agora.
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->conn->prepare(
            "INSERT INTO password_resets (user_id, token, expires_at, used) VALUES (?, ?, ?, FALSE)"
        );
        // "iss" -> integer, string, string
        $stmt->bind_param("iss", $user_id, $token, $expiresAt);

        return $stmt->execute();
    }

    /**
     * Busca e valida um token no banco de dados.
     * Retorna os dados do token apenas se ele existir, não tiver sido usado e não tiver expirado.
     * @param string $token O token a ser validado.
     * @return array|null Retorna os dados do token ou null se for inválido.
     */
    public function getToken($token)
    {
        // Consulta o token que não foi usado e cuja data de expiração é maior que o tempo atual.
        $sql = "SELECT * FROM password_resets WHERE token = ? AND used = FALSE AND expires_at > NOW()";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Invalida um token marcando-o como usado no banco de dados.
     * Esta é uma abordagem mais segura do que deletar, pois mantém um registro da utilização.
     * @param string $token O token a ser invalidado.
     * @return bool Retorna true em caso de sucesso, false em caso de falha.
     */
    public function invalidateToken($token)
    {
        $stmt = $this->conn->prepare("UPDATE password_resets SET used = TRUE WHERE token = ?");
        $stmt->bind_param("s", $token);

        return $stmt->execute();
    }
}
