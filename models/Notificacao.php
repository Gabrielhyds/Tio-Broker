<?php
// models/Notificacao.php

class Notificacao {

    private $db; // Conexão MySQLi

    /**
     * @param mysqli $db Conexão com o banco de dados MySQLi
     */
    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    /**
     * Retorna até $limite notificações não lidas de um usuário
     * @param int $id_usuario
     * @param int $limite
     * @return array
     */
    public function buscarNaoLidasPorUsuario(int $id_usuario, int $limite = 10): array {
        $sql = "SELECT * FROM notificacoes 
                WHERE id_usuario = ? AND lida = FALSE
                ORDER BY data_envio DESC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Erro preparar buscarNaoLidasPorUsuario: " . $this->db->error);
            return [];
        }

        $stmt->bind_param("ii", $id_usuario, $limite);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Conta todas as notificações não lidas de um usuário
     * @param int $id_usuario
     * @return int
     */
    public function contarNaoLidasPorUsuario(int $id_usuario): int {
        $sql = "SELECT COUNT(id_notificacao) AS total FROM notificacoes
                WHERE id_usuario = ? AND lida = FALSE";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Erro preparar contarNaoLidasPorUsuario: " . $this->db->error);
            return 0;
        }

        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        return $row ? (int)$row['total'] : 0;
    }

    /**
     * Marca todas as notificações de um usuário como lidas
     * @param int $id_usuario
     * @return bool
     */
    public function marcarTodasComoLidas(int $id_usuario): bool {
        $sql = "UPDATE notificacoes SET lida = TRUE WHERE id_usuario = ? AND lida = FALSE";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Erro preparar marcarTodasComoLidas: " . $this->db->error);
            return false;
        }
        $stmt->bind_param("i", $id_usuario);
        return $stmt->execute();
    }

    /**
     * Retorna até $limite notificações de um usuário (lidas e não lidas)
     * @param int $id_usuario
     * @param int $limite
     * @return array
     */
    public function buscarTodasPorUsuario(int $id_usuario, int $limite = 50): array {
        $sql = "SELECT * FROM notificacoes
                WHERE id_usuario = ?
                ORDER BY data_envio DESC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Erro preparar buscarTodasPorUsuario: " . $this->db->error);
            return [];
        }

        $stmt->bind_param("ii", $id_usuario, $limite);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    /**
     * **NOVO:** Cria uma nova notificação para um usuário.
     */
    public function criarNotificacao($id_usuario, $mensagem) {
        $sql = "INSERT INTO notificacoes (id_usuario, mensagem) VALUES (?, ?)";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            // Lidar com erro, talvez logar
            return false;
        }
        
        // 'i' para id_usuario (integer), 's' para mensagem (string)
        $stmt->bind_param("is", $id_usuario, $mensagem); 
        return $stmt->execute();
    }
}
?>
