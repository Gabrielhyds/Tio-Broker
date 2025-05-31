<?php
class PasswordResetModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function createToken($user_id, $token)
    {
        $stmt = $this->conn->prepare("INSERT INTO password_resets (user_id, token, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $user_id, $token);
        return $stmt->execute();
    }

    public function getToken($token)
    {
        $sql = "SELECT * FROM password_resets WHERE token = ? AND created_at >= (NOW() - INTERVAL 1 HOUR)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function deleteToken($token)
    {
        $stmt = $this->conn->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
    }
}
?>
