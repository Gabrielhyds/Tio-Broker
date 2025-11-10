<?php
class Usuario
{
    /**
     * Propriedade privada para armazenar a conexão com o banco de dados.
     * @var mysqli
     */
    private $conn;

    /**
     * Construtor da classe.
     * @param mysqli $conn A conexão com o banco de dados.
     */
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Cadastra um novo usuário no banco de dados.
     */
    public function cadastrar($nome, $email, $cpf, $telefone, $senha, $permissao, $id_imobiliaria, $creci = null, $foto = null)
    {
        $senha_hash = md5($senha); // AVISO: md5 é inseguro. Use password_hash().
        $stmt = $this->conn->prepare("INSERT INTO usuario (nome, email, cpf, telefone, senha, permissao, id_imobiliaria, creci, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt === false) {
            return false;
        }
        
        $stmt->bind_param("ssssssiss", $nome, $email, $cpf, $telefone, $senha_hash, $permissao, $id_imobiliaria, $creci, $foto);
        return $stmt->execute();
    }

    /**
     * Autentica um usuário.
     */
    public function login($email, $senha)
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE email = ? AND is_deleted = 0");

        if ($stmt === false) {
            return false;
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
     * Busca um único usuário ativo pelo seu ID.
     */
    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE id_usuario = ? AND is_deleted = 0");
        
        if ($stmt === false) {
            return null;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_assoc() : null;
    }

    /**
     * Atualiza o perfil de um usuário.
     */
    public function atualizarPerfil($id, $nome, $email, $telefone, $novaSenhaHash = null, $fotoPath = null)
    {
        $sqlParts = [];
        $params = [];
        $types = '';

        $sqlParts[] = "nome = ?";
        $params[] = $nome;
        $types .= 's';

        $sqlParts[] = "email = ?";
        $params[] = $email;
        $types .= 's';
        
        if ($telefone !== null) {
            $sqlParts[] = "telefone = ?";
            $params[] = $telefone;
            $types .= 's';
        }

        if ($novaSenhaHash !== null) {
            $sqlParts[] = "senha = ?";
            $params[] = $novaSenhaHash;
            $types .= 's';
        }

        if ($fotoPath !== null) {
            $sqlParts[] = "foto = ?";
            $params[] = $fotoPath;
            $types .= 's';
        }

        if (empty($sqlParts)) {
            return true; // Nada a atualizar
        }

        $sql = "UPDATE usuario SET " . implode(', ', $sqlParts) . " WHERE id_usuario = ?";
        $params[] = $id;
        $types .= 'i';

        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }

    /**
     * Atualiza os dados de um usuário existente (função de admin).
     */
    public function atualizar($id, $nome, $email, $cpf, $telefone, $permissao, $id_imobiliaria, $creci = null, $foto = null)
    {
        $stmt = $this->conn->prepare("UPDATE usuario SET nome = ?, email = ?, cpf = ?, telefone = ?, permissao = ?, id_imobiliaria = ?, creci = ?, foto = ? WHERE id_usuario = ?");
        
        if ($stmt === false) {
            return false;
        }
        
        $stmt->bind_param("sssssissi", $nome, $email, $cpf, $telefone, $permissao, $id_imobiliaria, $creci, $foto, $id);
        return $stmt->execute();
    }

    /**
     * "Exclui" um usuário logicamente (soft delete).
     */
    public function excluir($id)
    {
        $stmt = $this->conn->prepare("UPDATE usuario SET is_deleted = 1 WHERE id_usuario = ?");

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Lista usuários. Se um ID de imobiliária for fornecido, filtra por essa imobiliária.
     * Se o ID for null, lista todos os usuários do sistema (comportamento para SuperAdmin).
     *
     * @param int|null $id_imobiliaria O ID da imobiliária para filtrar, ou null para listar todos.
     * @return array Uma lista de usuários.
     */
    public function listarPorImobiliaria($id_imobiliaria)
    {
        $usuarios = [];
        $params = [];
        $types = '';

        $sql = "SELECT id_usuario, nome, email, permissao FROM usuario WHERE is_deleted = 0";

        if ($id_imobiliaria !== null) {
            $sql .= " AND id_imobiliaria = ?";
            $params[] = $id_imobiliaria;
            $types .= 'i';
        }

        $sql .= " ORDER BY nome ASC";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Falha no prepare (listarPorImobiliaria): " . $this->conn->error);
            return [];
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            error_log("Falha na execução (listarPorImobiliaria): " . $stmt->error);
            $stmt->close();
            return [];
        }

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        $stmt->close();
        return $usuarios;
    }

    /**
     * Lista imobiliárias que possuem usuários.
     */
    public function listarImobiliariasComUsuarios()
    {
        $query = "SELECT i.id_imobiliaria, i.nome FROM imobiliaria i JOIN usuario u ON u.id_imobiliaria = i.id_imobiliaria WHERE u.is_deleted = 0 AND i.is_deleted = 0 GROUP BY i.id_imobiliaria ORDER BY i.nome";
        $resultado = $this->conn->query($query);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Remove a associação de um usuário com uma imobiliária.
     */
    public function removerImobiliaria($id_usuario)
    {
        $stmt = $this->conn->prepare("UPDATE usuario SET id_imobiliaria = NULL WHERE id_usuario = ?");
        if ($stmt === false) return false;
        $stmt->bind_param("i", $id_usuario);
        return $stmt->execute();
    }

    /**
     * Vincula um usuário a uma imobiliária.
     */
    public function vincularImobiliaria($id_usuario, $id_imobiliaria)
    {
        $stmt = $this->conn->prepare("UPDATE usuario SET id_imobiliaria = ? WHERE id_usuario = ?");
        if ($stmt === false) return false;
        $stmt->bind_param("ii", $id_imobiliaria, $id_usuario);
        return $stmt->execute();
    }

    /**
     * Restaura um usuário que foi "excluído" logicamente.
     */
    public function restaurar($id)
    {
        $stmt = $this->conn->prepare("UPDATE usuario SET is_deleted = 0 WHERE id_usuario = ?");
        if ($stmt === false) return false;
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Conta o total de usuários excluídos, com filtro opcional.
     */
    public function contarTotalExcluidos($filtro = null)
    {
        $sql = "SELECT COUNT(u.id_usuario) as total FROM usuario u LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria WHERE u.is_deleted = 1";
        $params = [];
        $types = "";

        if (!empty($filtro)) {
            $sql .= " AND (u.nome LIKE ? OR u.email LIKE ?)";
            $searchTerm = "%{$filtro}%";
            $params = [$searchTerm, $searchTerm];
            $types = "ss";
        }

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return 0;

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        return (int)($resultado['total'] ?? 0);
    }

    /**
     * Lista usuários excluídos com paginação e filtro.
     */
    public function listarExcluidosPaginado($pagina_atual, $limite, $filtro = null)
    {
        $offset = ($pagina_atual - 1) * $limite;
        $sql = "SELECT u.*, i.nome AS nome_imobiliaria FROM usuario u LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria WHERE u.is_deleted = 1";
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
        if ($stmt === false) return [];

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Lista todos os usuários ativos.
     */
    public function listarTodos()
    {
        $sql = "SELECT * FROM usuario WHERE is_deleted = 0 ORDER BY nome ASC";
        $resultado = $this->conn->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Busca um usuário pelo e-mail, independentemente de estar ativo ou não.
     */
    public function buscarPorEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE email = ?");
        if ($stmt === false) return null;
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_assoc() : null;
    }

    /**
     * Atualiza a senha de um usuário.
     */
    public function atualizarSenha($id_usuario, $novaSenha)
    {
        $stmt = $this->conn->prepare("UPDATE usuario SET senha = ? WHERE id_usuario = ?");
        if ($stmt === false) return false;
        $stmt->bind_param("si", $novaSenha, $id_usuario);
        return $stmt->execute();
    }

    /**
     * Conta o total de usuários ativos com filtros.
     */
    public function contarTotal($filtro = null, $permissao = '', $id_imobiliaria = null)
    {
        $sql = "SELECT COUNT(u.id_usuario) as total FROM usuario u LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria WHERE u.is_deleted = 0";
        $params = [];
        $types = "";

        if ($permissao !== 'SuperAdmin' && $id_imobiliaria) {
            $sql .= " AND u.id_imobiliaria = ?";
            $params[] = $id_imobiliaria;
            $types .= "i";
        }

        if (!empty($filtro)) {
            $sql .= " AND (u.nome LIKE ? OR u.email LIKE ? OR u.permissao LIKE ? OR i.nome LIKE ?)";
            $searchTerm = "%{$filtro}%";
            array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
            $types .= "ssss";
        }

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return 0;

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        return (int)($resultado['total'] ?? 0);
    }

    /**
     * Lista usuários ativos com paginação e filtros.
     */
    public function listarPaginadoComImobiliaria($pagina_atual, $limite, $filtro = null, $permissao = '', $id_imobiliaria = null)
    {
        $offset = ($pagina_atual - 1) * $limite;
        $sql = "SELECT u.*, i.nome AS nome_imobiliaria FROM usuario u LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria WHERE u.is_deleted = 0";
        $params = [];
        $types = '';

        if ($permissao !== 'SuperAdmin' && $id_imobiliaria) {
            $sql .= " AND u.id_imobiliaria = ?";
            $params[] = $id_imobiliaria;
            $types .= "i";
        }

        if (!empty($filtro)) {
            $sql .= " AND (u.nome LIKE ? OR u.email LIKE ? OR u.permissao LIKE ? OR i.nome LIKE ?)";
            $searchTerm = "%{$filtro}%";
            array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
            $types .= "ssss";
        }

        $sql .= " ORDER BY u.nome ASC LIMIT ? OFFSET ?";
        $params[] = $limite;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return [];

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
}
