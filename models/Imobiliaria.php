<?php
class Imobiliaria
{
    private $conn; // Conexão com o banco de dados

    // Construtor recebe a conexão como parâmetro
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Cadastra uma nova imobiliária no banco de dados
     */
    public function cadastrar($nome, $cnpj)
    {
        $stmt = $this->conn->prepare("INSERT INTO imobiliaria (nome, cnpj) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome, $cnpj);
        return $stmt->execute();
    }

    // --- NOVO ---
    /**
     * Conta o total de imobiliárias cadastradas.
     * @return int Total de imobiliárias.
     */
    public function contarTotal()
    {
        $resultado = $this->conn->query("SELECT COUNT(id_imobiliaria) as total FROM imobiliaria");
        $dados = $resultado->fetch_assoc();
        return (int)($dados['total'] ?? 0);
    }

    // --- ALTERADO ---
    /**
     * Lista as imobiliárias de forma paginada.
     * @param int $pagina_atual O número da página atual.
     * @param int $limite O número de itens por página.
     * @return array Lista de imobiliárias para a página atual.
     */
    public function listarPaginado($pagina_atual, $limite)
    {
        // Calcula o offset para a consulta SQL
        $offset = ($pagina_atual - 1) * $limite;

        $query = "
            SELECT i.*, COUNT(u.id_usuario) as total_usuarios
            FROM imobiliaria i
            LEFT JOIN usuario u ON i.id_imobiliaria = u.id_imobiliaria
            GROUP BY i.id_imobiliaria
            ORDER BY i.nome ASC
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $limite, $offset);
        $stmt->execute();

        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }


    /**
     * Exclui uma imobiliária pelo ID
     */
    public function excluir($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM imobiliaria WHERE id_imobiliaria = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Busca uma imobiliária específica pelo ID
     */
    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM imobiliaria WHERE id_imobiliaria = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Atualiza os dados de uma imobiliária existente
     */
    public function atualizar($id, $nome, $cnpj)
    {
        $stmt = $this->conn->prepare("UPDATE imobiliaria SET nome = ?, cnpj = ? WHERE id_imobiliaria = ?");
        $stmt->bind_param("ssi", $nome, $cnpj, $id);
        return $stmt->execute();
    }

    /**
     * Verifica se uma imobiliária possui usuários vinculados.
     */
    public function temUsuariosVinculados($id_imobiliaria)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(id_usuario) as total FROM usuario WHERE id_imobiliaria = ?");
        $stmt->bind_param("i", $id_imobiliaria);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] > 0;
    }

    /**
     * Lista todas as imobiliárias para selects (sem paginação).
     */
    public function listarTodas()
    {
        $query = "SELECT id_imobiliaria, nome FROM imobiliaria ORDER BY nome ASC";
        $resultado = $this->conn->query($query);
        if (!$resultado) return [];
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
}
