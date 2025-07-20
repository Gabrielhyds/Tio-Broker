<?php
class Chat
{
    // Propriedade privada para armazenar a conexão com o banco de dados.
    private $conn;

    // O construtor da classe recebe o objeto de conexão como parâmetro.
    public function __construct($conexao)
    {
        // Atribui a conexão recebida à propriedade da classe.
        $this->conn = $conexao;
    }

    // Cria uma nova conversa do tipo 'privada' entre dois usuários.
    public function criarConversaPrivada($id_origem, $id_destino)
    {
        // Prepara e executa uma instrução SQL para inserir uma nova conversa na tabela 'conversas'.
        $stmt = $this->conn->prepare("INSERT INTO conversas (tipo_conversa) VALUES ('privada')");
        $stmt->execute();
        // Obtém o ID da conversa que acabou de ser criada.
        $id_conversa = $this->conn->insert_id;

        // Adiciona os dois usuários (origem e destino) à conversa recém-criada.
        $this->adicionarUsuarioNaConversa($id_conversa, $id_origem);
        $this->adicionarUsuarioNaConversa($id_conversa, $id_destino);

        // Retorna o ID da nova conversa.
        return $id_conversa;
    }

    // Adiciona um usuário a uma conversa específica na tabela de associação 'usuarios_conversa'.
    public function adicionarUsuarioNaConversa($id_conversa, $id_usuario)
    {
        // Prepara a instrução SQL para inserir o par (id_conversa, id_usuario).
        $stmt = $this->conn->prepare("INSERT INTO usuarios_conversa (id_conversa, id_usuario) VALUES (?, ?)");
        // Associa os parâmetros (bind) aos placeholders. 'i' indica que são inteiros.
        $stmt->bind_param("ii", $id_conversa, $id_usuario);
        // Executa a instrução.
        $stmt->execute();
    }

    // Verifica se já existe uma conversa privada entre dois usuários específicos.
    public function buscarConversaPrivadaEntre($id1, $id2)
    {
        // Query complexa que junta a tabela de conversas com a de usuários duas vezes
        // para encontrar uma conversa que contenha ambos os IDs de usuário.
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
        // Obtém o resultado e o retorna como um array associativo.
        $result = $stmt->get_result()->fetch_assoc();

        // Retorna o ID da conversa se encontrada, caso contrário, retorna null.
        return $result['id_conversa'] ?? null;
    }

    // Lista todas as conversas (privadas ou em grupo) das quais um usuário participa.
    public function listarConversasPorUsuario($id_usuario)
    {
        // Query para selecionar conversas com base no ID do usuário.
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

        // Obtém o conjunto de resultados.
        $result = $stmt->get_result();
        // Retorna todos os resultados como um array de arrays associativos, ou um array vazio se não houver resultados.
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // MÉTODO ATUALIZADO: Lista todas as mensagens de uma conversa, incluindo suas reações.
    public function listarMensagensDaConversa($id_conversa)
    {
        // 1. Busca todas as mensagens, juntando com a tabela de usuários para obter o nome e a foto do perfil.
        $query_mensagens = "
            SELECT 
                m.id_mensagem, m.id_conversa, m.id_usuario, m.mensagem, m.data_envio, m.lida, 
                u.nome AS nome_usuario, u.foto
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

        // 2. Extrai os IDs das mensagens para buscar as reações.
        $ids_mensagens = array_column($mensagens, 'id_mensagem');

        // 3. Busca todas as reações para as mensagens em uma única consulta.
        $reacoes = $this->buscarReacoesParaMensagens($ids_mensagens);

        // 4. Anexa as reações a cada mensagem correspondente.
        foreach ($mensagens as $key => $mensagem) {
            $mensagens[$key]['reacoes'] = $reacoes[$mensagem['id_mensagem']] ?? [];
        }

        return $mensagens;
    }

    // Envia uma nova mensagem para uma conversa específica.
    public function enviarMensagem($id_conversa, $id_usuario, $mensagem)
    {
        // Prepara a instrução para inserir a nova mensagem.
        $stmt = $this->conn->prepare("
            INSERT INTO mensagens (id_conversa, id_usuario, mensagem)
            VALUES (?, ?, ?)
        ");
        // Associa os parâmetros: dois inteiros ('i') e uma string ('s').
        $stmt->bind_param("iis", $id_conversa, $id_usuario, $mensagem);
        // Executa e retorna true em caso de sucesso, ou false em caso de falha.
        return $stmt->execute();
    }

    // Em uma conversa privada, retorna o ID do outro participante (o destinatário).
    public function obterDestinatarioDaConversa($id_conversa, $id_usuario_atual)
    {
        // Busca na tabela de associação o usuário que está na mesma conversa mas não é o usuário atual.
        $stmt = $this->conn->prepare("
            SELECT id_usuario 
            FROM usuarios_conversa 
            WHERE id_conversa = ? AND id_usuario != ?
            LIMIT 1
        ");
        $stmt->bind_param("ii", $id_conversa, $id_usuario_atual);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        // Retorna o ID do destinatário ou null se não for encontrado.
        return $result['id_usuario'] ?? null;
    }

    // Marca como lidas (lida = 1) todas as mensagens de uma conversa que não foram enviadas pelo usuário logado.
    public function marcarComoLidas($id_conversa, $id_usuario_logado)
    {
        // Prepara a instrução de atualização.
        $stmt = $this->conn->prepare("
            UPDATE mensagens 
            SET lida = 1 
            WHERE id_conversa = ? 
            AND id_usuario != ? 
            AND lida = 0
        ");
        $stmt->bind_param("ii", $id_conversa, $id_usuario_logado);
        // Executa a atualização.
        $stmt->execute();
    }

    // Conta o número de mensagens não lidas, agrupadas por quem as enviou.
    public function contarNaoLidasPorRemetente($id_usuario_logado)
    {
        // Query para contar mensagens não lidas recebidas pelo usuário logado.
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

        // Cria um array onde a chave é o ID do remetente e o valor é o total de mensagens não lidas.
        $notificacoes = [];
        while ($row = $result->fetch_assoc()) {
            $notificacoes[$row['remetente']] = $row['total'];
        }
        return $notificacoes;
    }

    // Busca a última mensagem trocada em cada conversa privada do usuário logado.
    public function buscarUltimaMensagemCom($id_logado)
    {
        // Query complexa para identificar o "outro usuário" em cada conversa e pegar a última mensagem.
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

        // Itera sobre os resultados (ordenados por data) e guarda apenas a primeira (a mais recente) para cada "outro_usuario".
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
        // Tenta inserir uma nova reação. Se a chave primária (id_mensagem, id_usuario) já existir, atualiza a coluna 'reacao'.
        $sql = "INSERT INTO reacoes (id_mensagem, id_usuario, reacao) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE reacao = ?";
        $stmt = $this->conn->prepare($sql);
        // Binda os parâmetros: id_mensagem, id_usuario, reacao (para o INSERT) e reacao (para o UPDATE).
        $stmt->bind_param("iiss", $id_mensagem, $id_usuario, $reacao, $reacao);
        return $stmt->execute();
    }

    /**
     * Busca todas as reações para uma lista de IDs de mensagens.
     * Agrupa as reações por emoji para contagem e lista os nomes dos usuários.
     */
    public function buscarReacoesParaMensagens(array $ids_mensagens)
    {
        // Se a lista de IDs estiver vazia, retorna um array vazio para evitar erros de SQL.
        if (empty($ids_mensagens)) {
            return [];
        }
        // Cria uma string de placeholders (?) correspondente ao número de IDs de mensagens.
        $placeholders = implode(',', array_fill(0, count($ids_mensagens), '?'));

        // Query que busca reações, conta o total por tipo de emoji e concatena os nomes dos usuários que reagiram.
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

        // Cria a string de tipos ('i' para cada ID de mensagem) e binda os parâmetros dinamicamente.
        $types = str_repeat('i', count($ids_mensagens));
        $stmt->bind_param($types, ...$ids_mensagens);

        $stmt->execute();
        $result = $stmt->get_result();

        // Organiza os resultados em um array onde a chave principal é o ID da mensagem.
        $reacoes_agrupadas = [];
        while ($row = $result->fetch_assoc()) {
            $reacoes_agrupadas[$row['id_mensagem']][] = $row;
        }
        return $reacoes_agrupadas;
    }
    
}
