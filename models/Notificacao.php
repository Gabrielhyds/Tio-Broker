<?php
// models/Notificacao.php

class Notificacao {
    
    private $db; // Armazena a conexão MySQLi

    /**
     * @param mysqli $db A conexão com o banco de dados MySQLi
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Busca todas as notificações não lidas de um usuário. (MySQLi)
     */
    public function buscarNaoLidasPorUsuario($id_usuario) {
        $sql = "SELECT * FROM notificacoes 
                WHERE id_usuario = ? AND lida = FALSE 
                ORDER BY data_envio DESC 
                LIMIT 10";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            // Tratar erro na preparação
            return [];
        }
        
        $stmt->bind_param("i", $id_usuario); // 'i' para integer
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Conta todas as notificações não lidas de um usuário. (MySQLi)
     */
    public function contarNaoLidasPorUsuario($id_usuario) {
        $sql = "SELECT COUNT(id_notificacao) AS total FROM notificacoes 
                WHERE id_usuario = ? AND lida = FALSE";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $resultado = $result->fetch_assoc();
        return $resultado ? (int)$resultado['total'] : 0;
    }

    /**
     * Marca todas as notificações de um usuário como lidas. (MySQLi)
     */
    public function marcarTodasComoLidas($id_usuario) {
        $sql = "UPDATE notificacoes SET lida = TRUE WHERE id_usuario = ? AND lida = FALSE";
        
        $stmt = $this->db->prepare($sql);
         if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("i", $id_usuario);
        return $stmt->execute();
    }
}
?>