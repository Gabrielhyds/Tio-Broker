<?php
class Usuario
{
    // Propriedade privada para armazenar a conexão com o banco de dados.
    private $conn;

    // Construtor da classe, que recebe a conexão como parâmetro ao ser instanciada.
    public function __construct($conn)
    {
        // Atribui a conexão recebida à propriedade da classe.
        $this->conn = $conn;
    }

    /**
     * Cadastra um novo usuário no banco de dados.
     */
    public function cadastrar($nome, $email, $cpf, $telefone, $senha, $permissao, $id_imobiliaria, $creci = null, $foto = null)
    {
        // Criptografa a senha usando MD5. (AVISO: md5 é obsoleto e inseguro; use password_hash() em projetos reais).
        $senha_hash = md5($senha);
        // Prepara a instrução SQL para inserir um novo usuário.
        $stmt = $this->conn->prepare("INSERT INTO usuario (nome, email, cpf, telefone, senha, permissao, id_imobiliaria, creci, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // Associa (bind) os parâmetros à instrução SQL. 's' para string, 'i' para integer.
        $stmt->bind_param("ssssssiss", $nome, $email, $cpf, $telefone, $senha_hash, $permissao, $id_imobiliaria, $creci, $foto);
        // Executa a instrução e retorna true ou false.
        return $stmt->execute();
    }

    /**
     * Autentica um usuário com e-mail e senha, ignorando usuários "excluídos".
     */
    public function login($email, $senha)
    {
        // Prepara a consulta para buscar um usuário ativo pelo e-mail.
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE email = ? AND is_deleted = 0");

        // Se a preparação da consulta falhar, interrompe a execução com um erro.
        if (!$stmt) {
            die("Erro ao preparar login: " . $this->conn->error);
        }

        // Associa o e-mail à consulta.
        $stmt->bind_param("s", $email);
        $stmt->execute();
        // Obtém o resultado da consulta.
        $resultado = $stmt->get_result();

        // Se nenhum usuário for encontrado, retorna falso.
        if ($resultado->num_rows === 0) {
            return false;
        }

        // Obtém os dados do usuário como um array associativo.
        $usuario = $resultado->fetch_assoc();

        // Compara a senha fornecida (criptografada com md5) com a senha armazenada no banco. Retorna os dados do usuário se for igual, senão retorna false.
        return (md5($senha) === $usuario['senha']) ? $usuario : false;
    }

    /**
     * Busca um único usuário ativo pelo seu ID.
     */
    public function buscarPorId($id)
    {
        // Prepara a busca por um usuário específico que não esteja deletado.
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE id_usuario = ? AND is_deleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Atualiza o perfil de um usuário no banco de dados.
     * Esta versão constrói a query SQL dinamicamente e inclui depuração de erros.
     */
    public function atualizarPerfil($id, $nome, $email, $telefone, $novaSenhaHash = null, $fotoPath = null)
    {
        // 1. Inicia a construção da query SQL e dos parâmetros
        $sqlParts = [];
        $params = [];
        $types = '';

        // 2. Adiciona os campos base que sempre serão atualizados
        $sqlParts[] = "nome = ?";
        $params[] = $nome;
        $types .= 's';

        $sqlParts[] = "email = ?";
        $params[] = $email;
        $types .= 's';

        // --- INÍCIO DA CORREÇÃO ---
        // Adiciona o telefone à query APENAS se um valor não nulo foi fornecido.
        // Isso evita o erro "Column 'telefone' cannot be null".
        if ($telefone !== null) {
            $sqlParts[] = "telefone = ?";
            $params[] = $telefone;
            $types .= 's';
        }
        // --- FIM DA CORREÇÃO ---

        // Adiciona a senha à query APENAS se uma nova senha foi fornecida.
        if ($novaSenhaHash !== null) {
            $sqlParts[] = "senha = ?";
            $params[] = $novaSenhaHash;
            $types .= 's';
        }

        // Adiciona a foto à query.
        $sqlParts[] = "foto = ?";
        $params[] = $fotoPath;
        $types .= 's';

        // Monta a query final
        $sql = "UPDATE usuario SET " . implode(', ', $sqlParts) . " WHERE id_usuario = ?";

        // Adiciona o ID do usuário aos parâmetros no final
        $params[] = $id;
        $types .= 'i';

        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            $_SESSION['erro'] = "ERRO DE SQL (prepare): " . $this->conn->error;
            return false;
        }

        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            return true; // Sucesso
        } else {
            $_SESSION['erro'] = "ERRO DE SQL (execute): " . $stmt->error;
            return false;
        }
    }
    // ✅✅✅ FIM DA CORREÇÃO ✅✅✅

    /**
     * Atualiza os dados de um usuário existente (função de admin).
     */
    public function atualizar($id, $nome, $email, $cpf, $telefone, $permissao, $id_imobiliaria, $creci = null, $foto = null)
    {
        $stmt = $this->conn->prepare("UPDATE usuario SET nome = ?, email = ?, cpf = ?, telefone = ?, permissao = ?, id_imobiliaria = ?, creci = ?, foto = ? WHERE id_usuario = ?");
        $stmt->bind_param("sssssissi", $nome, $email, $cpf, $telefone, $permissao, $id_imobiliaria, $creci, $foto, $id);
        return $stmt->execute();
    }

    /**
     * "Exclui" um usuário logicamente (soft delete).
     */
    public function excluir($id)
    {
        // Prepara um UPDATE para marcar o usuário como deletado.
        $stmt = $this->conn->prepare("UPDATE usuario SET is_deleted = 1 WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // ... (restante das suas funções existentes, sem alterações) ...

    public function listarPorImobiliaria($id_imobiliaria)
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE id_imobiliaria = ? AND is_deleted = 0 ORDER BY nome ASC");
        $stmt->bind_param("i", $id_imobiliaria);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

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

    public function removerImobiliaria($id_usuario)
    {
        $stmt = $this->conn->prepare("UPDATE usuario SET id_imobiliaria = NULL WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        return $stmt->execute();
    }

    public function vincularImobiliaria($id_usuario, $id_imobiliaria)
    {
        $stmt = $this->conn->prepare("UPDATE usuario SET id_imobiliaria = ? WHERE id_usuario = ?");
        $stmt->bind_param("ii", $id_imobiliaria, $id_usuario);
        return $stmt->execute();
    }

    public function restaurar($id)
    {
        $stmt = $this->conn->prepare("UPDATE usuario SET is_deleted = 0 WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

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

    public function listarTodos()
    {
        $sql = "SELECT * FROM usuario WHERE is_deleted = 0 ORDER BY nome ASC";
        $resultado = $this->conn->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function buscarPorEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function atualizarSenha($id_usuario, $novaSenha)
    {
        $stmt = $this->conn->prepare("UPDATE usuario SET senha = ? WHERE id_usuario = ?");
        $stmt->bind_param("si", $novaSenha, $id_usuario);
        return $stmt->execute();
    }
    public function contarTotal($filtro = null, $permissao = '', $id_imobiliaria = null)
    {
        $sql = "SELECT COUNT(u.id_usuario) as total 
                FROM usuario u
                LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria
                WHERE u.is_deleted = 0";

        $params = [];
        $types = "";

        // Adiciona filtro por permissão
        if ($permissao !== 'SuperAdmin' && $id_imobiliaria) {
            $sql .= " AND u.id_imobiliaria = ?";
            $params[] = $id_imobiliaria;
            $types .= "i";
        }

        // Adiciona filtro de busca textual
        if (!empty($filtro)) {
            $sql .= " AND (u.nome LIKE ? OR u.email LIKE ? OR u.permissao LIKE ? OR i.nome LIKE ?)";
            $searchTerm = "%{$filtro}%";
            array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
            $types .= "ssss";
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
     * Lista os usuários de forma paginada, aplicando filtros de permissão e busca.
     */
    public function listarPaginadoComImobiliaria($pagina_atual, $limite, $filtro = null, $permissao = '', $id_imobiliaria = null)
    {
        $offset = ($pagina_atual - 1) * $limite;
        $sql = "
            SELECT u.*, i.nome AS nome_imobiliaria
            FROM usuario u
            LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria
            WHERE u.is_deleted = 0
        ";
        $params = [];
        $types = '';

        // Filtro por permissão
        if ($permissao !== 'SuperAdmin' && $id_imobiliaria) {
            $sql .= " AND u.id_imobiliaria = ?";
            $params[] = $id_imobiliaria;
            $types .= "i";
        }

        // Filtro de busca textual
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
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
}
