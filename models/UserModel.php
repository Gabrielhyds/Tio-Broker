<?php
class UserModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function findByEmail($email)
    {
    $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
        }

    public function updatePassword($user_id, $newPassword)
    {
        $stmt = $this->conn->prepare("UPDATE usuario SET senha = ? WHERE id = ?");
        $stmt->bind_param("si", $newPassword, $user_id);
        return $stmt->execute();
    }
}
?>
