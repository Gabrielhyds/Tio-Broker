<?php
class Usuario
{
    private $conn; // Armazena a conexão com o banco de dados

    // Construtor que recebe a conexão como parâmetro
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Cadastra um novo usuário no banco
     */
    public function cadastrar($nome, $email, $cpf, $telefone, $senha, $permissao, $id_imobiliaria, $creci = null, $foto = null)
    {
        $senha_hash = md5($senha); // Criptografa a senha (uso de md5 não é recomendado para produção)
        $stmt = $this->conn->prepare("INSERT INTO usuario (nome, email, cpf, telefone, senha, permissao, id_imobiliaria, creci, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssiss", $nome, $email, $cpf, $telefone, $senha_hash, $permissao, $id_imobiliaria, $creci, $foto);
        return $stmt->execute();
    }

    /**
     * Faz login com email e senha
     */
    public function login($email, $senha)
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE email = ?");

        if (!$stmt) {
            die("Erro ao preparar login: " . $this->conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {
            return false; // Nenhum usuário encontrado
        }

        $usuario = $resultado->fetch_assoc();

        if (md5($senha) === $usuario['senha']) {
            return $usuario; // Senha correta
        }

        return false; // Senha incorreta
    }

    /**
     * Conta o total de usuários cadastrados, com filtro opcional.
     * @param string|null $filtro Termo para buscar nos campos visíveis.
     * @return int Total de usuários.
     */
    public function contarTotal($filtro = null)
    {
        $sql = "SELECT COUNT(u.id_usuario) as total 
                FROM usuario u
                LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria";
        $params = [];
        $types = "";

        if (!empty($filtro)) {
            $sql .= " WHERE u.nome LIKE ? OR u.email LIKE ? OR u.permissao LIKE ? OR i.nome LIKE ?";
            $searchTerm = "%{$filtro}%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
            $types = "ssss";
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
     * Lista os usuários de forma paginada com o nome da imobiliária, com filtro opcional.
     * @param int $pagina_atual O número da página atual.
     * @param int $limite O número de itens por página.
     * @param string|null $filtro Termo para buscar nos campos visíveis.
     * @return array Lista de usuários para a página atual.
     */
    public function listarPaginadoComImobiliaria($pagina_atual, $limite, $filtro = null)
    {
        $offset = ($pagina_atual - 1) * $limite;
        $sql = "
            SELECT u.*, i.nome AS nome_imobiliaria
            FROM usuario u
            LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria
        ";
        $params = [];
        $types = '';

        if (!empty($filtro)) {
            $sql .= " WHERE u.nome LIKE ? OR u.email LIKE ? OR u.permissao LIKE ? OR i.nome LIKE ?";
            $searchTerm = "%{$filtro}%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
            $types = "ssss";
        }

        $sql .= " ORDER BY u.nome ASC LIMIT ? OFFSET ?";
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
     * Lista todos os usuários com o nome da imobiliária associada (para selects e outros usos sem paginação)
     */
    public function listarTodosComImobiliaria()
    {
        $query = "
            SELECT u.*, i.nome AS nome_imobiliaria
            FROM usuario u
            LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria
            ORDER BY u.nome ASC
        ";
        $resultado = $this->conn->query($query);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Busca um único usuário pelo ID
     */
    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Atualiza os dados de um usuário existente
     */
    public function atualizar($id, $nome, $email, $cpf, $telefone, $permissao, $id_imobiliaria, $creci = null, $foto = null)
    {
        $stmt = $this->conn->prepare("UPDATE usuario SET nome = ?, email = ?, cpf = ?, telefone = ?, permissao = ?, id_imobiliaria = ?, creci = ?, foto = ? WHERE id_usuario = ?");
        $stmt->bind_param("sssssissi", $nome, $email, $cpf, $telefone, $permissao, $id_imobiliaria, $creci, $foto, $id);
        return $stmt->execute();
    }

    /**
     * Exclui um usuário pelo ID
     */
    public function excluir($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM usuario WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Lista todos os usuários de uma determinada imobiliária
     */
    public function listarPorImobiliaria($id_imobiliaria)
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE id_imobiliaria = ? ORDER BY nome ASC");
        $stmt->bind_param("i", $id_imobiliaria);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Retorna todas as imobiliárias que possuem ao menos um usuário
     */
    public function listarImobiliariasComUsuarios()
    {
        $query = "
            SELECT i.id_imobiliaria, i.nome 
            FROM imobiliaria i
            JOIN usuario u ON u.id_imobiliaria = i.id_imobiliaria
            GROUP BY i.id_imobiliaria
            ORDER BY i.nome
        ";
        $resultado = $this->conn->query($query);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Retira o vínculo de um usuário com a imobiliária (seta id_imobiliaria = NULL)
     */
    public function removerImobiliaria($id_usuario)
    {
        $stmt = $this->conn->prepare("
            UPDATE usuario
            SET id_imobiliaria = NULL
            WHERE id_usuario = ?
        ");
        $stmt->bind_param("i", $id_usuario);
        return $stmt->execute();
    }


    /**
     * Vincula um usuário a uma imobiliária
     */
    public function vincularImobiliaria($id_usuario, $id_imobiliaria)
    {
        $stmt = $this->conn->prepare("
            UPDATE usuario
            SET id_imobiliaria = ?
            WHERE id_usuario = ?
        ");
        $stmt->bind_param("ii", $id_imobiliaria, $id_usuario);
        return $stmt->execute();
    }
}
