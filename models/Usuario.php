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
     * Faz login com email e senha, ignorando usuários "excluídos".
     */
    public function login($email, $senha)
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE email = ? AND is_deleted = 0");

        if (!$stmt) {
            die("Erro ao preparar login: " . $this->conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {
            return false;
        }

        $usuario = $resultado->fetch_assoc();

        return (md5($senha) === $usuario['senha']) ? $usuario : false;
    }

    /**
     * Conta o total de usuários ativos (não excluídos), com filtro opcional.
     */
    public function contarTotal($filtro = null)
    {
        // --- CORRIGIDO --- Adicionado `i.is_deleted = 0` na junção para mais robustez
        $sql = "SELECT COUNT(u.id_usuario) as total 
                FROM usuario u
                LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria AND i.is_deleted = 0
                WHERE u.is_deleted = 0";
        $params = [];
        $types = "";

        if (!empty($filtro)) {
            $sql .= " AND (u.nome LIKE ? OR u.email LIKE ? OR u.permissao LIKE ? OR i.nome LIKE ?)";
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
     * Lista os usuários ativos de forma paginada, com filtro opcional.
     */
    public function listarPaginadoComImobiliaria($pagina_atual, $limite, $filtro = null)
    {
        $offset = ($pagina_atual - 1) * $limite;
        // --- CORRIGIDO --- Adicionado `i.is_deleted = 0` na junção para mais robustez
        $sql = "
            SELECT u.*, i.nome AS nome_imobiliaria
            FROM usuario u
            LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria AND i.is_deleted = 0
            WHERE u.is_deleted = 0
        ";
        $params = [];
        $types = '';

        if (!empty($filtro)) {
            $sql .= " AND (u.nome LIKE ? OR u.email LIKE ? OR u.permissao LIKE ? OR i.nome LIKE ?)";
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
     * Lista todos os usuários ativos com o nome da imobiliária associada.
     */
    public function listarTodosComImobiliaria()
    {
        // --- CORRIGIDO --- Adicionado `i.is_deleted = 0` na junção
        $query = "
            SELECT u.*, i.nome AS nome_imobiliaria
            FROM usuario u
            LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria AND i.is_deleted = 0
            WHERE u.is_deleted = 0
            ORDER BY u.nome ASC
        ";
        $resultado = $this->conn->query($query);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Busca um único usuário ativo pelo ID.
     */
    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE id_usuario = ? AND is_deleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Atualiza os dados de um usuário existente.
     */
    public function atualizar($id, $nome, $email, $cpf, $telefone, $permissao, $id_imobiliaria, $creci = null, $foto = null)
    {
        $stmt = $this->conn->prepare("UPDATE usuario SET nome = ?, email = ?, cpf = ?, telefone = ?, permissao = ?, id_imobiliaria = ?, creci = ?, foto = ? WHERE id_usuario = ?");
        $stmt->bind_param("sssssissi", $nome, $email, $cpf, $telefone, $permissao, $id_imobiliaria, $creci, $foto, $id);
        return $stmt->execute();
    }

    /**
     * "Exclui" um usuário logicamente, definindo o campo 'is_deleted' para 1.
     */
    public function excluir($id)
    {
        $stmt = $this->conn->prepare("UPDATE usuario SET is_deleted = 1 WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Lista todos os usuários ativos de uma determinada imobiliária.
     */
    public function listarPorImobiliaria($id_imobiliaria)
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE id_imobiliaria = ? AND is_deleted = 0 ORDER BY nome ASC");
        $stmt->bind_param("i", $id_imobiliaria);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Retorna todas as imobiliárias que possuem ao menos um usuário ativo.
     */
    public function listarImobiliariasComUsuarios()
    {
        $query = "
            SELECT i.id_imobiliaria, i.nome 
            FROM imobiliaria i
            JOIN usuario u ON u.id_imobiliaria = i.id_imobiliaria
            WHERE u.is_deleted = 0 AND i.is_deleted = 0
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
        $stmt = $this->conn->prepare("UPDATE usuario SET id_imobiliaria = NULL WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        return $stmt->execute();
    }


    /**
     * Vincula um usuário a uma imobiliária
     */
    public function vincularImobiliaria($id_usuario, $id_imobiliaria)
    {
        $stmt = $this->conn->prepare("UPDATE usuario SET id_imobiliaria = ? WHERE id_usuario = ?");
        $stmt->bind_param("ii", $id_imobiliaria, $id_usuario);
        return $stmt->execute();
    }

    // --- MÉTODOS NOVOS PARA RESTAURAÇÃO E LISTAGEM DE EXCLUÍDOS ---

    /**
     * Restaura um usuário que foi "excluído" logicamente.
     */
    public function restaurar($id)
    {
        $stmt = $this->conn->prepare("UPDATE usuario SET is_deleted = 0 WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Conta o total de usuários "excluídos", com filtro opcional.
     */
    public function contarTotalExcluidos($filtro = null)
    {
        $sql = "SELECT COUNT(u.id_usuario) as total 
                FROM usuario u
                LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria
                WHERE u.is_deleted = 1";
        $params = [];
        $types = "";

        if (!empty($filtro)) {
            $sql .= " AND (u.nome LIKE ? OR u.email LIKE ?)";
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
     * Lista os usuários "excluídos" de forma paginada, com filtro opcional.
     */
    public function listarExcluidosPaginado($pagina_atual, $limite, $filtro = null)
    {
        $offset = ($pagina_atual - 1) * $limite;
        $sql = "
            SELECT u.*, i.nome AS nome_imobiliaria
            FROM usuario u
            LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria
            WHERE u.is_deleted = 1
        ";
        $params = [];
        $types = '';

        if (!empty($filtro)) {
            $sql .= " AND (u.nome LIKE ? OR u.email LIKE ?)";
            $searchTerm = "%{$filtro}%";
            $params = [$searchTerm, $searchTerm];
            $types = "ss";
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
}
