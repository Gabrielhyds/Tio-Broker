<?php
class Chat {
    private $conn;

    public function __construct($conexao) {
        $this->conn = $conexao;
    }

    public function criarConversaPrivada($id_origem, $id_destino) {
        $stmt = $this->conn->prepare("INSERT INTO conversas (tipo_conversa) VALUES ('privada')");
        $stmt->execute();
        $id_conversa = $this->conn->insert_id;
    
        $this->adicionarUsuarioNaConversa($id_conversa, $id_origem);
        $this->adicionarUsuarioNaConversa($id_conversa, $id_destino);
    
        return $id_conversa;
    }
    

    public function adicionarUsuarioNaConversa($id_conversa, $id_usuario) {
        $stmt = $this->conn->prepare("INSERT INTO usuarios_conversa (id_conversa, id_usuario) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_conversa, $id_usuario);
        $stmt->execute();
    }

    public function buscarConversaPrivadaEntre($id1, $id2) {
        $query = "
            SELECT c.id_conversa
            FROM conversas c
            JOIN usuarios_conversa uc1 ON c.id_conversa = uc1.id_conversa
            JOIN usuarios_conversa uc2 ON c.id_conversa = uc2.id_conversa
            WHERE c.tipo_conversa = 'privada'
              AND uc1.id_usuario = ?
              AND uc2.id_usuario = ?
            GROUP BY c.id_conversa
            LIMIT 1
        ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $id1, $id2);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
    
        return $result['id_conversa'] ?? null;
    }

    public function listarConversasPorUsuario($id_usuario) {
        $query = "
            SELECT c.id_conversa, c.nome_conversa
            FROM conversas c
            JOIN usuarios_conversa uc ON c.id_conversa = uc.id_conversa
            WHERE uc.id_usuario = ?
            ORDER BY c.data_criacao DESC
        ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
    
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function listarMensagensDaConversa($id_conversa) {
        $query = "
            SELECT m.*, u.nome AS nome_usuario
            FROM mensagens m
            JOIN usuario u ON m.id_usuario = u.id_usuario
            WHERE m.id_conversa = ?
            ORDER BY m.data_envio ASC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id_conversa);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }


    public function enviarMensagem($id_conversa, $id_usuario, $mensagem) {
        $stmt = $this->conn->prepare("
            INSERT INTO mensagens (id_conversa, id_usuario, mensagem)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iis", $id_conversa, $id_usuario, $mensagem);
        return $stmt->execute();
    }
    // Retorna o outro participante da conversa privada
    public function obterDestinatarioDaConversa($id_conversa, $id_usuario_atual) {
        $stmt = $this->conn->prepare("
            SELECT id_usuario 
            FROM usuarios_conversa 
            WHERE id_conversa = ? AND id_usuario != ?
            LIMIT 1
        ");
        $stmt->bind_param("ii", $id_conversa, $id_usuario_atual);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['id_usuario'] ?? null;
    }
    public function marcarComoLidas($id_conversa, $id_usuario_logado) {
        $stmt = $this->conn->prepare("
            UPDATE mensagens 
            SET lida = 1 
            WHERE id_conversa = ? 
            AND id_usuario != ? 
            AND lida = 0
        ");
        $stmt->bind_param("ii", $id_conversa, $id_usuario_logado);
        $stmt->execute();
    }

    public function contarNaoLidasPorRemetente($id_usuario_logado) {
        $query = "
            SELECT m.id_usuario AS remetente, COUNT(*) AS total
            FROM mensagens m
            JOIN conversas c ON m.id_conversa = c.id_conversa
            JOIN usuarios_conversa uc ON uc.id_conversa = c.id_conversa
            WHERE uc.id_usuario = ?
              AND m.id_usuario != ?
              AND m.lida = 0
            GROUP BY m.id_usuario
        ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $id_usuario_logado, $id_usuario_logado);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $notificacoes = [];
        while ($row = $result->fetch_assoc()) {
            $notificacoes[$row['remetente']] = $row['total'];
        }
        return $notificacoes;
    }
    
    public function buscarUltimaMensagemCom($id_logado) {
        $query = "
            SELECT 
                CASE 
                    WHEN m.id_usuario = ? THEN uc2.id_usuario
                    ELSE m.id_usuario
                END AS outro_usuario,
                m.mensagem,
                m.data_envio
            FROM mensagens m
            JOIN conversas c ON m.id_conversa = c.id_conversa
            JOIN usuarios_conversa uc1 ON c.id_conversa = uc1.id_conversa AND uc1.id_usuario = ?
            JOIN usuarios_conversa uc2 ON c.id_conversa = uc2.id_conversa AND uc2.id_usuario != uc1.id_usuario
            WHERE c.tipo_conversa = 'privada'
            ORDER BY m.data_envio DESC
        ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $id_logado, $id_logado);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $ultimas = [];
        while ($row = $result->fetch_assoc()) {
            if (!isset($ultimas[$row['outro_usuario']])) {
                $ultimas[$row['outro_usuario']] = $row;
            }
        }
    
        return $ultimas;
    }

}
?>
