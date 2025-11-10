<?php
// Caminho: models/Chat.php
namespace App\Models;

class Chat
{
    /** @var \mysqli */
    private $conn;

    public function __construct(\mysqli $conexao)
    {
        $this->conn = $conexao;
    }

    /* ------------------------- Utils MySQLi (sem mysqlnd) ------------------------- */

    /** Retorna true se get_result estiver disponível (mysqlnd). */
    private function hasGetResult(): bool
    {
        return method_exists('mysqli_stmt', 'get_result');
    }

    /** fetch all assoc sem mysqlnd */
    private function fetchAllAssocNoNd(\mysqli_stmt $stmt): array
    {
        $result = [];
        if (!$meta = $stmt->result_metadata()) {
            return $result;
        }
        $row = [];
        $bind = [];
        while ($field = $meta->fetch_field()) {
            $row[$field->name] = null;
            $bind[] = &$row[$field->name];
        }
        call_user_func_array([$stmt, 'bind_result'], $bind);
        while ($stmt->fetch()) {
            // copia o array para não manter referências
            $result[] = array_map(fn($v) => $v, $row);
        }
        $meta->free_result();
        return $result;
    }

    /** fetch uma linha assoc sem mysqlnd */
    private function fetchOneAssocNoNd(\mysqli_stmt $stmt): ?array
    {
        $rows = $this->fetchAllAssocNoNd($stmt);
        return $rows[0] ?? null;
    }

    /* ------------------------------ Conversas ------------------------------ */

    public function criarConversaPrivada(int $id_origem, int $id_destino): int
    {
        // evita duplicar conversa privada da mesma dupla
        if ($existente = $this->buscarConversaPrivadaEntre($id_origem, $id_destino)) {
            return (int)$existente;
        }

        $stmt = $this->conn->prepare("INSERT INTO conversas (tipo_conversa) VALUES ('privada')");
        if (!$stmt) { throw new \Exception("Prepare falhou: ".$this->conn->error); }
        $stmt->execute();
        $id_conversa = (int)$this->conn->insert_id;
        $stmt->close();

        $this->adicionarUsuarioNaConversa($id_conversa, $id_origem);
        $this->adicionarUsuarioNaConversa($id_conversa, $id_destino);

        return $id_conversa;
    }

    public function adicionarUsuarioNaConversa(int $id_conversa, int $id_usuario): void
    {
        $stmt = $this->conn->prepare("INSERT IGNORE INTO usuarios_conversa (id_conversa, id_usuario) VALUES (?, ?)");
        if (!$stmt) { throw new \Exception("Prepare falhou: ".$this->conn->error); }
        $stmt->bind_param("ii", $id_conversa, $id_usuario);
        $stmt->execute();
        $stmt->close();
    }

    public function buscarConversaPrivadaEntre(int $id1, int $id2): ?int
    {
        // garante exatamente os 2 participantes
        $sql = "
            SELECT c.id_conversa
            FROM conversas c
            JOIN usuarios_conversa uc ON c.id_conversa = uc.id_conversa
            WHERE c.tipo_conversa = 'privada'
              AND uc.id_usuario IN (?, ?)
            GROUP BY c.id_conversa
            HAVING COUNT(DISTINCT uc.id_usuario) = 2
            LIMIT 1
        ";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { throw new \Exception("Prepare falhou: ".$this->conn->error); }
        $stmt->bind_param("ii", $id1, $id2);
        $stmt->execute();

        if ($this->hasGetResult()) {
            $res = $stmt->get_result()->fetch_assoc();
        } else {
            $res = $this->fetchOneAssocNoNd($stmt);
        }
        $stmt->close();

        return isset($res['id_conversa']) ? (int)$res['id_conversa'] : null;
    }

    public function listarConversasPorUsuario(int $id_usuario): array
    {
        $sql = "
            SELECT c.id_conversa, c.nome_conversa, c.tipo_conversa, c.data_criacao
            FROM conversas c
            JOIN usuarios_conversa uc ON c.id_conversa = uc.id_conversa
            WHERE uc.id_usuario = ?
            ORDER BY c.data_criacao DESC
        ";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { throw new \Exception("Prepare falhou: ".$this->conn->error); }
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();

        $rows = $this->hasGetResult()
            ? ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: [])
            : $this->fetchAllAssocNoNd($stmt);

        $stmt->close();
        return $rows;
    }

    public function obterDestinatarioDaConversa(int $id_conversa, int $id_usuario_atual): ?int
    {
        $sql = "
            SELECT id_usuario 
            FROM usuarios_conversa 
            WHERE id_conversa = ? AND id_usuario != ?
            LIMIT 1
        ";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { throw new \Exception("Prepare falhou: ".$this->conn->error); }
        $stmt->bind_param("ii", $id_conversa, $id_usuario_atual);
        $stmt->execute();

        if ($this->hasGetResult()) {
            $res = $stmt->get_result()->fetch_assoc();
        } else {
            $res = $this->fetchOneAssocNoNd($stmt);
        }
        $stmt->close();

        return isset($res['id_usuario']) ? (int)$res['id_usuario'] : null;
    }

    /* ------------------------------ Mensagens ------------------------------ */

    public function listarMensagensDaConversa(int $id_conversa): array
    {
        $sql = "
            SELECT 
                m.id_mensagem, m.id_conversa, m.id_usuario, m.mensagem, m.data_envio, 
                m.editada_em, CAST(m.apagada AS UNSIGNED) AS apagada,
                u.nome AS nome_usuario, u.foto
            FROM mensagens m
            JOIN usuario u ON m.id_usuario = u.id_usuario
            WHERE m.id_conversa = ?
            ORDER BY m.data_envio ASC
        ";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { throw new \Exception("Prepare falhou: ".$this->conn->error); }
        $stmt->bind_param("i", $id_conversa);
        $stmt->execute();

        $mensagens = $this->hasGetResult()
            ? ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: [])
            : $this->fetchAllAssocNoNd($stmt);

        $stmt->close();

        if (empty($mensagens)) return [];

        $ids = array_column($mensagens, 'id_mensagem');
        $reacoes = $this->buscarReacoesParaMensagens($ids);

        foreach ($mensagens as &$m) {
            $id = (int)$m['id_mensagem'];
            $m['reacoes'] = $reacoes[$id] ?? [];
        }
        unset($m);

        return $mensagens;
    }

    public function enviarMensagem(int $id_conversa, int $id_usuario, string $mensagem): int
    {
        $sql = "INSERT INTO mensagens (id_conversa, id_usuario, mensagem) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { throw new \Exception("Prepare falhou: ".$this->conn->error); }
        $stmt->bind_param("iis", $id_conversa, $id_usuario, $mensagem);
        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            throw new \Exception("Erro ao executar INSERT mensagem: ".$err);
        }
        $id = (int)$this->conn->insert_id;
        $stmt->close();
        return $id;
    }

    public function marcarComoLidas(int $id_conversa, int $id_usuario_logado): void
    {
        $sql = "
            UPDATE mensagens 
            SET lida = 1 
            WHERE id_conversa = ? 
              AND id_usuario != ? 
              AND lida = 0
        ";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { throw new \Exception("Prepare falhou: ".$this->conn->error); }
        $stmt->bind_param("ii", $id_conversa, $id_usuario_logado);
        $stmt->execute();
        $stmt->close();
    }

    public function contarNaoLidasPorRemetente(int $id_usuario_logado): array
    {
        $sql = "
            SELECT m.id_usuario AS remetente, COUNT(*) AS total
            FROM mensagens m
            JOIN conversas c ON m.id_conversa = c.id_conversa
            JOIN usuarios_conversa uc ON uc.id_conversa = c.id_conversa
            WHERE uc.id_usuario = ?
              AND m.id_usuario != ?
              AND m.lida = 0
            GROUP BY m.id_usuario
        ";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { throw new \Exception("Prepare falhou: ".$this->conn->error); }
        $stmt->bind_param("ii", $id_usuario_logado, $id_usuario_logado);
        $stmt->execute();

        $rows = $this->hasGetResult()
            ? ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: [])
            : $this->fetchAllAssocNoNd($stmt);

        $stmt->close();

        $out = [];
        foreach ($rows as $r) {
            $out[(int)$r['remetente']] = (int)$r['total'];
        }
        return $out;
    }

    public function buscarUltimaMensagemCom(int $id_logado): array
    {
        $sql = "
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
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { throw new \Exception("Prepare falhou: ".$this->conn->error); }
        $stmt->bind_param("ii", $id_logado, $id_logado);
        $stmt->execute();

        $rows = $this->hasGetResult()
            ? ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: [])
            : $this->fetchAllAssocNoNd($stmt);

        $stmt->close();

        $ultimas = [];
        foreach ($rows as $r) {
            $k = (int)$r['outro_usuario'];
            if (!isset($ultimas[$k])) {
                $ultimas[$k] = $r;
            }
        }
        return $ultimas;
    }

    /* ------------------------------- Reações ------------------------------- */

    public function adicionarOuAtualizarReacao(int $id_mensagem, int $id_usuario, string $reacao): bool
    {
        $sql = "INSERT INTO reacoes (id_mensagem, id_usuario, reacao) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE reacao = VALUES(reacao)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { throw new \Exception("Prepare falhou: ".$this->conn->error); }
        $stmt->bind_param("iis", $id_mensagem, $id_usuario, $reacao);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function buscarReacoesParaMensagens(array $ids_mensagens): array
    {
        if (empty($ids_mensagens)) return [];
        // monta placeholders
        $placeholders = implode(',', array_fill(0, count($ids_mensagens), '?'));
        $sql = "
            SELECT 
                r.id_mensagem, 
                r.reacao, 
                COUNT(r.id_reacao) AS total,
                GROUP_CONCAT(u.nome SEPARATOR ', ') AS nomes_usuarios
            FROM reacoes r
            JOIN usuario u ON r.id_usuario = u.id_usuario
            WHERE r.id_mensagem IN ($placeholders)
            GROUP BY r.id_mensagem, r.reacao
            ORDER BY r.id_mensagem, total DESC
        ";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { throw new \Exception("Prepare falhou: ".$this->conn->error); }
        // tipos dinâmicos
        $types = str_repeat('i', count($ids_mensagens));
        $stmt->bind_param($types, ...$ids_mensagens);
        $stmt->execute();

        $rows = $this->hasGetResult()
            ? ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: [])
            : $this->fetchAllAssocNoNd($stmt);

        $stmt->close();

        $out = [];
        foreach ($rows as $r) {
            $out[(int)$r['id_mensagem']][] = $r;
        }
        return $out;
    }

    public function editarMensagem(int $id_mensagem, int $id_usuario, string $nova_mensagem): bool
    {
        $sql = "UPDATE mensagens SET mensagem = ?, editada_em = NOW() WHERE id_mensagem = ? AND id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { throw new \Exception("Prepare falhou: ".$this->conn->error); }
        $stmt->bind_param("sii", $nova_mensagem, $id_mensagem, $id_usuario);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0;
        $stmt->close();
        return $ok;
    }

    public function apagarMensagem(int $id_mensagem, int $id_usuario): bool
    {
        $sql = "UPDATE mensagens SET mensagem = 'Esta mensagem foi apagada.', apagada = TRUE WHERE id_mensagem = ? AND id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { throw new \Exception("Prepare falhou: ".$this->conn->error); }
        $stmt->bind_param("ii", $id_mensagem, $id_usuario);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0;
        $stmt->close();
        return $ok;
    }
}
