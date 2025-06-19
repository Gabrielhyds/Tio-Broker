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
     * @param string $nome Nome da imobiliária
     * @param string $cnpj CNPJ da imobiliária
     * @return bool Sucesso ou falha no cadastro
     */
    public function cadastrar($nome, $cnpj)
    {
        $stmt = $this->conn->prepare("INSERT INTO imobiliaria (nome, cnpj) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome, $cnpj);
        return $stmt->execute();
    }

    /**
     * Lista todas as imobiliárias cadastradas
     * Retorna também a quantidade de usuários vinculados a cada imobiliária
     * @return array Lista de imobiliárias com total de usuários
     */
    public function listarTodas()
    {
        $query = "
            SELECT i.*, COUNT(u.id_usuario) as total_usuarios
            FROM imobiliaria i
            LEFT JOIN usuario u ON i.id_imobiliaria = u.id_imobiliaria
            GROUP BY i.id_imobiliaria
            ORDER BY i.nome ASC
        ";
        $resultado = $this->conn->query($query);
        if (!$resultado) return [];
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Exclui uma imobiliária pelo ID
     * @param int $id ID da imobiliária
     * @return bool Sucesso ou falha na exclusão
     */
    public function excluir($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM imobiliaria WHERE id_imobiliaria = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Busca uma imobiliária específica pelo ID
     * @param int $id ID da imobiliária
     * @return array|null Dados da imobiliária ou null se não encontrada
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
     * @param int $id ID da imobiliária
     * @param string $nome Novo nome
     * @param string $cnpj Novo CNPJ
     * @return bool Sucesso ou falha na atualização
     */
    public function atualizar($id, $nome, $cnpj)
    {
        $stmt = $this->conn->prepare("UPDATE imobiliaria SET nome = ?, cnpj = ? WHERE id_imobiliaria = ?");
        $stmt->bind_param("ssi", $nome, $cnpj, $id);
        return $stmt->execute();
    }

    // --- NOVO ---
    /**
     * Verifica se uma imobiliária possui usuários vinculados.
     * @param int $id_imobiliaria O ID da imobiliária a ser verificada.
     * @return bool Retorna true se houver usuários, false caso contrário.
     */
    public function temUsuariosVinculados($id_imobiliaria)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(id_usuario) as total FROM usuario WHERE id_imobiliaria = ?");
        $stmt->bind_param("i", $id_imobiliaria);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        // Retorna true se a contagem for maior que 0.
        return $result['total'] > 0;
    }
}
