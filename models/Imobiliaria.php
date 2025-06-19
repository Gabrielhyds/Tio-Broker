<?php
class Imobiliaria
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function cadastrar($nome, $cnpj)
    {
        $stmt = $this->conn->prepare("INSERT INTO imobiliaria (nome, cnpj) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome, $cnpj);
        return $stmt->execute();
    }

    /**
     * Conta o total de imobiliárias, com filtro opcional.
     * @param string|null $filtro Termo para buscar no ID, nome ou CNPJ.
     * @return int Total de imobiliárias.
     */
    public function contarTotal($filtro = null)
    {
        $sql = "SELECT COUNT(id_imobiliaria) as total FROM imobiliaria";
        $params = [];
        $types = "";

        if (!empty($filtro)) {
            $sql .= " WHERE nome LIKE ? OR cnpj LIKE ?";
            $searchTerm = "%{$filtro}%";
            $params = [$searchTerm, "%" . str_replace(['.', '/', '-'], '', $filtro) . "%"];
            $types = "ss";

            if (is_numeric($filtro)) {
                $sql .= " OR id_imobiliaria = ?";
                $params[] = (int)$filtro;
                $types .= "i";
            }
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
     * Lista as imobiliárias de forma paginada, com filtro opcional.
     * @param int $pagina_atual O número da página atual.
     * @param int $limite O número de itens por página.
     * @param string|null $filtro Termo para buscar no ID, nome ou CNPJ.
     * @return array Lista de imobiliárias para a página atual.
     */
    public function listarPaginado($pagina_atual, $limite, $filtro = null)
    {
        $offset = ($pagina_atual - 1) * $limite;
        $sql = "
            SELECT i.*, COUNT(u.id_usuario) as total_usuarios
            FROM imobiliaria i
            LEFT JOIN usuario u ON i.id_imobiliaria = u.id_imobiliaria
        ";
        $params = [];
        $types = '';

        if (!empty($filtro)) {
            $sql .= " WHERE i.nome LIKE ? OR i.cnpj LIKE ?";
            $searchTerm = "%{$filtro}%";
            $params = [$searchTerm, "%" . str_replace(['.', '/', '-'], '', $filtro) . "%"];
            $types = "ss";

            if (is_numeric($filtro)) {
                $sql .= " OR i.id_imobiliaria = ?";
                $params[] = (int)$filtro;
                $types .= "i";
            }
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

    public function excluir($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM imobiliaria WHERE id_imobiliaria = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM imobiliaria WHERE id_imobiliaria = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function atualizar($id, $nome, $cnpj)
    {
        $stmt = $this->conn->prepare("UPDATE imobiliaria SET nome = ?, cnpj = ? WHERE id_imobiliaria = ?");
        $stmt->bind_param("ssi", $nome, $cnpj, $id);
        return $stmt->execute();
    }

    public function temUsuariosVinculados($id_imobiliaria)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(id_usuario) as total FROM usuario WHERE id_imobiliaria = ?");
        $stmt->bind_param("i", $id_imobiliaria);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] > 0;
    }

    public function listarTodas()
    {
        $query = "SELECT id_imobiliaria, nome FROM imobiliaria ORDER BY nome ASC";
        $resultado = $this->conn->query($query);
        if (!$resultado) return [];
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
}
