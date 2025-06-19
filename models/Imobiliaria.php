<?php
class Imobiliaria
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Cadastra uma nova imobiliária no banco de dados.
     */
    public function cadastrar($nome, $cnpj)
    {
        $stmt = $this->conn->prepare("INSERT INTO imobiliaria (nome, cnpj) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome, $cnpj);
        return $stmt->execute();
    }

    /**
     * Conta o total de imobiliárias ativas, com filtro opcional.
     */
    public function contarTotal($filtro = null)
    {
        // --- ALTERADO --- Condição base agora é `is_deleted = 0`
        $sql = "SELECT COUNT(id_imobiliaria) as total FROM imobiliaria WHERE is_deleted = 0";
        $params = [];
        $types = "";

        if (!empty($filtro)) {
            $sql .= " AND (nome LIKE ? OR cnpj LIKE ? OR id_imobiliaria = ?)";
            $searchTerm = "%{$filtro}%";
            $numericFilter = is_numeric($filtro) ? (int)$filtro : 0;
            $params = [$searchTerm, str_replace(['.', '/', '-'], '', $searchTerm), $numericFilter];
            $types = "ssi";
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        return (int)($resultado['total'] ?? 0);
    }

    /**
     * Lista as imobiliárias ativas de forma paginada, com filtro opcional.
     */
    public function listarPaginado($pagina_atual, $limite, $filtro = null)
    {
        $offset = ($pagina_atual - 1) * $limite;
        // --- ALTERADO --- Condições para buscar apenas registros ativos (i.is_deleted = 0 e u.is_deleted = 0)
        $sql = "
            SELECT i.*, COUNT(u.id_usuario) as total_usuarios
            FROM imobiliaria i
            LEFT JOIN usuario u ON i.id_imobiliaria = u.id_imobiliaria AND u.is_deleted = 0
            WHERE i.is_deleted = 0
        ";
        $params = [];
        $types = '';

        if (!empty($filtro)) {
            $sql .= " AND (i.nome LIKE ? OR i.cnpj LIKE ? OR i.id_imobiliaria = ?)";
            $searchTerm = "%{$filtro}%";
            $numericFilter = is_numeric($filtro) ? (int)$filtro : 0;
            $params = [$searchTerm, str_replace(['.', '/', '-'], '', $searchTerm), $numericFilter];
            $types = "ssi";
        }

        $sql .= " GROUP BY i.id_imobiliaria ORDER BY i.nome ASC LIMIT ? OFFSET ?";
        $params[] = $limite;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * "Exclui" uma imobiliária logicamente, definindo is_deleted = 1.
     */
    public function excluir($id)
    {
        // --- ALTERADO --- Atualiza o campo is_deleted para 1
        $stmt = $this->conn->prepare("UPDATE imobiliaria SET is_deleted = 1 WHERE id_imobiliaria = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Busca uma imobiliária ativa específica pelo ID.
     */
    public function buscarPorId($id)
    {
        // --- ALTERADO --- Apenas busca imobiliárias com is_deleted = 0
        $stmt = $this->conn->prepare("SELECT * FROM imobiliaria WHERE id_imobiliaria = ? AND is_deleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Atualiza os dados de uma imobiliária existente.
     */
    public function atualizar($id, $nome, $cnpj)
    {
        $stmt = $this->conn->prepare("UPDATE imobiliaria SET nome = ?, cnpj = ? WHERE id_imobiliaria = ?");
        $stmt->bind_param("ssi", $nome, $cnpj, $id);
        return $stmt->execute();
    }

    /**
     * Verifica se uma imobiliária possui usuários ativos vinculados.
     */
    public function temUsuariosVinculados($id_imobiliaria)
    {
        // --- ALTERADO --- Conta apenas usuários com is_deleted = 0
        $stmt = $this->conn->prepare("SELECT COUNT(id_usuario) as total FROM usuario WHERE id_imobiliaria = ? AND is_deleted = 0");
        $stmt->bind_param("i", $id_imobiliaria);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] > 0;
    }

    /**
     * Lista todas as imobiliárias ativas para selects.
     */
    public function listarTodas()
    {
        // --- ALTERADO --- Apenas lista imobiliárias com is_deleted = 0
        $query = "SELECT id_imobiliaria, nome FROM imobiliaria WHERE is_deleted = 0 ORDER BY nome ASC";
        $resultado = $this->conn->query($query);
        if (!$resultado) return [];
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // --- MÉTODOS NOVOS PARA RESTAURAÇÃO E LISTAGEM DE EXCLUÍDOS ---

    /**
     * Restaura uma imobiliária que foi "excluída" logicamente.
     */
    public function restaurar($id)
    {
        $stmt = $this->conn->prepare("UPDATE imobiliaria SET is_deleted = 0 WHERE id_imobiliaria = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Conta o total de imobiliárias "excluídas", com filtro opcional.
     */
    public function contarTotalExcluidos($filtro = null)
    {
        $sql = "SELECT COUNT(id_imobiliaria) as total FROM imobiliaria WHERE is_deleted = 1";
        $params = [];
        $types = "";

        if (!empty($filtro)) {
            $sql .= " AND (nome LIKE ? OR cnpj LIKE ?)";
            $searchTerm = "%{$filtro}%";
            $params = [$searchTerm, $searchTerm];
            $types = "ss";
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        return (int)($resultado['total'] ?? 0);
    }

    /**
     * Lista as imobiliárias "excluídas" de forma paginada.
     */
    public function listarExcluidosPaginado($pagina_atual, $limite, $filtro = null)
    {
        $offset = ($pagina_atual - 1) * $limite;
        $sql = "SELECT * FROM imobiliaria WHERE is_deleted = 1";
        $params = [];
        $types = '';

        if (!empty($filtro)) {
            $sql .= " AND (nome LIKE ? OR cnpj LIKE ?)";
            $searchTerm = "%{$filtro}%";
            $params = [$searchTerm, $searchTerm];
            $types = "ss";
        }

        $sql .= " ORDER BY nome ASC LIMIT ? OFFSET ?";
        $params[] = $limite;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
}
