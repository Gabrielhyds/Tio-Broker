<?php
class Chat
{
    private $conn; // Conexão com o banco de dados

    // Construtor recebe a conexão
    public function __construct($conexao)
    {
        $this->conn = $conexao;
    }

    // Cria uma nova conversa privada entre dois usuários
    public function criarConversaPrivada($id_origem, $id_destino)
    {
        $stmt = $this->conn->prepare("INSERT INTO conversas (tipo_conversa) VALUES ('privada')");
        $stmt->execute();
        $id_conversa = $this->conn->insert_id; // Obtém o ID da conversa recém-criada

        // Adiciona os dois usuários na conversa
        $this->adicionarUsuarioNaConversa($id_conversa, $id_origem);
        $this->adicionarUsuarioNaConversa($id_conversa, $id_destino);

        return $id_conversa;
    }

    // Adiciona um usuário a uma conversa
    public function adicionarUsuarioNaConversa($id_conversa, $id_usuario)
    {
        $stmt = $this->conn->prepare("INSERT INTO usuarios_conversa (id_conversa, id_usuario) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_conversa, $id_usuario);
        $stmt->execute();
    }

    // Verifica se já existe uma conversa privada entre dois usuários
    public function buscarConversaPrivadaEntre($id1, $id2)
    {
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

    // Lista todas as conversas do usuário
    public function listarConversasPorUsuario($id_usuario)
    {
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

    // MÉTODO ATUALIZADO: Lista todas as mensagens E SUAS REAÇÕES
    public function listarMensagensDaConversa($id_conversa)
    {
        // 1. Buscar todas as mensagens da conversa
        $query_mensagens = "
            SELECT m.id_mensagem, m.id_conversa, m.id_usuario, m.mensagem, m.data_envio, m.lida, u.nome AS nome_usuario
            FROM mensagens m
            JOIN usuario u ON m.id_usuario = u.id_usuario
            WHERE m.id_conversa = ?
            ORDER BY m.data_envio ASC
        ";
        $stmt_mensagens = $this->conn->prepare($query_mensagens);
        $stmt_mensagens->bind_param("i", $id_conversa);
        $stmt_mensagens->execute();
        $result_mensagens = $stmt_mensagens->get_result();
        $mensagens = $result_mensagens ? $result_mensagens->fetch_all(MYSQLI_ASSOC) : [];

        if (empty($mensagens)) {
            return [];
        }

        // 2. Extrair os IDs das mensagens para buscar as reações
        $ids_mensagens = array_column($mensagens, 'id_mensagem');

        // 3. Buscar todas as reações para essas mensagens em uma única consulta
        $reacoes = $this->buscarReacoesParaMensagens($ids_mensagens);

        // 4. Anexar as reações a cada mensagem correspondente
        foreach ($mensagens as $key => $mensagem) {
            $mensagens[$key]['reacoes'] = $reacoes[$mensagem['id_mensagem']] ?? [];
        }

        return $mensagens;
    }

    // Envia uma mensagem em uma conversa
    public function enviarMensagem($id_conversa, $id_usuario, $mensagem)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO mensagens (id_conversa, id_usuario, mensagem)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iis", $id_conversa, $id_usuario, $mensagem);
        return $stmt->execute();
    }

    // Retorna o ID do outro usuário em uma conversa privada
    public function obterDestinatarioDaConversa($id_conversa, $id_usuario_atual)
    {
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

    // Marca como lidas todas as mensagens da conversa que ainda não foram lidas e não são do usuário atual
    public function marcarComoLidas($id_conversa, $id_usuario_logado)
    {
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

    // Retorna um array com o número de mensagens não lidas por remetente
    public function contarNaoLidasPorRemetente($id_usuario_logado)
    {
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

    // Retorna a última mensagem trocada com cada usuário
    public function buscarUltimaMensagemCom($id_logado)
    {
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

    // --- NOVOS MÉTODOS PARA REAÇÕES ---

    /**
     * Adiciona ou atualiza uma reação de um usuário a uma mensagem.
     * Usa "ON DUPLICATE KEY UPDATE" para trocar a reação se o usuário já tiver reagido.
     */
    public function adicionarOuAtualizarReacao($id_mensagem, $id_usuario, $reacao)
    {
        $sql = "INSERT INTO reacoes (id_mensagem, id_usuario, reacao) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE reacao = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiss", $id_mensagem, $id_usuario, $reacao, $reacao);
        return $stmt->execute();
    }

    /**
     * Busca todas as reações para uma lista de IDs de mensagens.
     * Agrupa as reações por emoji para contagem e lista os nomes dos usuários.
     */
    public function buscarReacoesParaMensagens(array $ids_mensagens)
    {
        if (empty($ids_mensagens)) {
            return [];
        }
        // Prepara os placeholders (?) para a cláusula IN
        $placeholders = implode(',', array_fill(0, count($ids_mensagens), '?'));

        $sql = "
            SELECT 
                r.id_mensagem, 
                r.reacao, 
                COUNT(r.id_reacao) as total,
                GROUP_CONCAT(u.nome SEPARATOR ', ') as nomes_usuarios
            FROM reacoes r
            JOIN usuario u ON r.id_usuario = u.id_usuario
            WHERE r.id_mensagem IN ($placeholders)
            GROUP BY r.id_mensagem, r.reacao
            ORDER BY r.id_mensagem, total DESC
        ";

        $stmt = $this->conn->prepare($sql);

        // Cria a string de tipos ('i' para cada ID) e binda os parâmetros
        $types = str_repeat('i', count($ids_mensagens));
        $stmt->bind_param($types, ...$ids_mensagens);

        $stmt->execute();
        $result = $stmt->get_result();

        $reacoes_agrupadas = [];
        while ($row = $result->fetch_assoc()) {
            $reacoes_agrupadas[$row['id_mensagem']][] = $row;
        }
        return $reacoes_agrupadas;
    }
}