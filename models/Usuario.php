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
     * Conta o total de usuários ativos (não excluídos), com filtros opcionais de busca e permissão.
     */
    public function contarTotal($filtro = null, $permissao = 'Admin', $id_imobiliaria = null)
    {
        // SQL base para contar usuários ativos.
        $sql = "SELECT COUNT(u.id_usuario) as total 
                FROM usuario u
                LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria AND i.is_deleted = 0
                WHERE u.is_deleted = 0";

        // Inicializa arrays para parâmetros dinâmicos.
        $params = [];
        $types = "";

        // Se o usuário não for 'SuperAdmin', restringe a contagem à sua própria imobiliária.
        if ($permissao !== 'SuperAdmin' && $id_imobiliaria !== null) {
            $sql .= " AND u.id_imobiliaria = ?";
            $params[] = $id_imobiliaria;
            $types .= "i";
        }

        // Se um termo de filtro for fornecido, adiciona condições de busca.
        if (!empty($filtro)) {
            $sql .= " AND (u.nome LIKE ? OR u.email LIKE ? OR u.permissao LIKE ? OR i.nome LIKE ?)";
            $searchTerm = "%{$filtro}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            $types .= "ssss";
        }

        // Prepara e executa a contagem com os parâmetros dinâmicos.
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        // Retorna o total como um inteiro.
        return (int)($resultado['total'] ?? 0);
    }

    /**
     * Lista os usuários ativos de forma paginada, com filtros opcionais.
     */
    public function listarPaginadoComImobiliaria($pagina_atual, $limite, $filtro = null, $permissao = 'Admin', $id_imobiliaria = null)
    {
        // Calcula o offset para a consulta SQL com base na página atual e no limite.
        $offset = ($pagina_atual - 1) * $limite;
        // SQL base para listar usuários ativos com o nome da imobiliária.
        $sql = "
        SELECT u.*, i.nome AS nome_imobiliaria
        FROM usuario u
        LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria AND i.is_deleted = 0
        WHERE u.is_deleted = 0
    ";
        $params = [];
        $types = '';

        // Aplica o filtro por imobiliária se o usuário não for SuperAdmin.
        if ($permissao !== 'SuperAdmin' && $id_imobiliaria !== null) {
            $sql .= " AND u.id_imobiliaria = ?";
            $params[] = $id_imobiliaria;
            $types .= "i";
        }

        // Aplica o filtro de busca textual, se fornecido.
        if (!empty($filtro)) {
            $sql .= " AND (u.nome LIKE ? OR u.email LIKE ? OR u.permissao LIKE ? OR i.nome LIKE ?)";
            $searchTerm = "%{$filtro}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            $types .= "ssss";
        }

        // Adiciona a ordenação e os limites de paginação.
        $sql .= " ORDER BY u.nome ASC LIMIT ? OFFSET ?";
        $params[] = $limite;
        $params[] = $offset;
        $types .= "ii";

        // Prepara, executa e retorna os resultados.
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
        // Query para buscar todos os usuários que não foram excluídos logicamente.
        $query = "
            SELECT u.*, i.nome AS nome_imobiliaria
            FROM usuario u
            LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria AND i.is_deleted = 0
            WHERE u.is_deleted = 0
            ORDER BY u.nome ASC
        ";
        // Executa a query diretamente (é segura pois não há input do usuário).
        $resultado = $this->conn->query($query);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
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
     * Atualiza os dados de um usuário existente.
     */
    public function atualizar($id, $nome, $email, $cpf, $telefone, $permissao, $id_imobiliaria, $creci = null, $foto = null)
    {
        // Prepara o UPDATE para os dados do usuário.
        $stmt = $this->conn->prepare("UPDATE usuario SET nome = ?, email = ?, cpf = ?, telefone = ?, permissao = ?, id_imobiliaria = ?, creci = ?, foto = ? WHERE id_usuario = ?");
        // Associa os parâmetros à instrução.
        $stmt->bind_param("sssssissi", $nome, $email, $cpf, $telefone, $permissao, $id_imobiliaria, $creci, $foto, $id);
        return $stmt->execute();
    }

    /**
     * "Exclui" um usuário logicamente (soft delete), definindo o campo 'is_deleted' para 1.
     */
    public function excluir($id)
    {
        // Prepara um UPDATE para marcar o usuário como deletado.
        $stmt = $this->conn->prepare("UPDATE usuario SET is_deleted = 1 WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Lista todos os usuários ativos de uma determinada imobiliária.
     */
    public function listarPorImobiliaria($id_imobiliaria)
    {
        // Prepara a busca de usuários ativos por ID de imobiliária.
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
        // Query que retorna imobiliárias distintas que têm pelo menos um usuário ativo.
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
     * Retira o vínculo de um usuário com a imobiliária (define id_imobiliaria como NULL).
     */
    public function removerImobiliaria($id_usuario)
    {
        // Prepara um UPDATE para desvincular o usuário.
        $stmt = $this->conn->prepare("UPDATE usuario SET id_imobiliaria = NULL WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        return $stmt->execute();
    }


    /**
     * Vincula um usuário a uma imobiliária.
     */
    public function vincularImobiliaria($id_usuario, $id_imobiliaria)
    {
        // Prepara um UPDATE para definir o ID da imobiliária para o usuário.
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
        // Prepara um UPDATE para reverter o soft delete, definindo is_deleted = 0.
        $stmt = $this->conn->prepare("UPDATE usuario SET is_deleted = 0 WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Conta o total de usuários "excluídos", com filtro opcional.
     */
    public function contarTotalExcluidos($filtro = null)
    {
        // SQL base para contar usuários marcados como deletados.
        $sql = "SELECT COUNT(u.id_usuario) as total 
                FROM usuario u
                LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria
                WHERE u.is_deleted = 1";
        $params = [];
        $types = "";

        // Adiciona filtro de busca textual, se fornecido.
        if (!empty($filtro)) {
            $sql .= " AND (u.nome LIKE ? OR u.email LIKE ?)";
            $searchTerm = "%{$filtro}%";
            $params = [$searchTerm, $searchTerm];
            $types = "ss";
        }

        // Prepara, executa e retorna a contagem.
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
        // Calcula o offset para paginação.
        $offset = ($pagina_atual - 1) * $limite;
        // SQL base para listar usuários deletados.
        $sql = "
            SELECT u.*, i.nome AS nome_imobiliaria
            FROM usuario u
            LEFT JOIN imobiliaria i ON u.id_imobiliaria = i.id_imobiliaria
            WHERE u.is_deleted = 1
        ";
        $params = [];
        $types = '';

        // Adiciona filtro de busca textual.
        if (!empty($filtro)) {
            $sql .= " AND (u.nome LIKE ? OR u.email LIKE ?)";
            $searchTerm = "%{$filtro}%";
            $params = [$searchTerm, $searchTerm];
            $types = "ss";
        }

        // Adiciona ordenação e limites de paginação.
        $sql .= " ORDER BY u.nome ASC LIMIT ? OFFSET ?";
        $params[] = $limite;
        $params[] = $offset;
        $types .= "ii";

        // Prepara, executa e retorna os resultados.
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
    /**
     * Lista todos os usuários ativos (sem imobiliária, sem filtros extras).
     */
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
    public function atualizarPerfil($id, $nome, $telefone, $novaSenha = null, $foto = null)
    {
        $sql = "UPDATE usuario SET nome = ?, telefone = ?";
        $tipos = "ss";
        $params = [$nome, $telefone];

        if ($novaSenha) {
            $sql .= ", senha = ?";
            $tipos .= "s";
            $params[] = password_hash($novaSenha, PASSWORD_DEFAULT);
        }

        if ($foto) {
            $sql .= ", foto = ?";
            $tipos .= "s";
            $params[] = $foto;
        }

        $sql .= " WHERE id_usuario = ?";
        $tipos .= "i";
        $params[] = $id;

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($tipos, ...$params);
        return $stmt->execute();
    }
}
