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

    // ✅✅✅ INÍCIO DA CORREÇÃO DEFINITIVA ✅✅✅
    /**
     * Atualiza o perfil de um usuário no banco de dados.
     * Esta versão constrói a query SQL dinamicamente e inclui depuração de erros.
     *
     * @param int $id O ID do usuário a ser atualizado.
     * @param string $nome O novo nome do usuário.
     * @param string $telefone O novo telefone do usuário.
     * @param string|null $novaSenhaHash A nova senha já criptografada (ou null se não for para alterar).
     * @param string|null $fotoPath O novo caminho da foto (pode ser null para remover a foto).
     * @return bool Retorna true se a atualização for bem-sucedida, false caso contrário.
     */
    public function atualizarPerfil($id, $nome, $telefone, $novaSenhaHash = null, $fotoPath = null)
    {
        // 1. Inicia a construção da query SQL e dos parâmetros
        $sqlParts = [];
        $params = [];
        $types = '';

        // 2. Adiciona os campos base que sempre serão atualizados
        $sqlParts[] = "nome = ?";
        $params[] = $nome;
        $types .= 's';

        $sqlParts[] = "telefone = ?";
        $params[] = $telefone;
        $types .= 's';

        // 3. Adiciona a senha à query APENAS se uma nova senha foi fornecida.
        // O controller já envia a senha criptografada (md5, neste caso).
        // Esta função apenas salva o valor recebido.
        if ($novaSenhaHash !== null) {
            $sqlParts[] = "senha = ?";
            $params[] = $novaSenhaHash;
            $types .= 's';
        }

        // 4. Adiciona a foto à query.
        // A lógica de upload/remoção está no Controller. Aqui apenas salvamos o resultado.
        // Se $fotoPath for null, a foto será removida do banco.
        // É importante que este campo seja sempre atualizado, mesmo que com o valor antigo.
        $sqlParts[] = "foto = ?";
        $params[] = $fotoPath;
        $types .= 's';

        // 5. Monta a query final
        $sql = "UPDATE usuario SET " . implode(', ', $sqlParts) . " WHERE id_usuario = ?";

        // Adiciona o ID do usuário aos parâmetros no final
        $params[] = $id;
        $types .= 'i';

        // 6. Prepara a statement
        $stmt = $this->conn->prepare($sql);

        // 7. DEPURAÇÃO: Verifica se a preparação da query falhou
        if ($stmt === false) {
            // Se falhar, armazena o erro na sessão para ser exibido na view.
            $_SESSION['erro'] = "ERRO DE SQL (prepare): " . $this->conn->error;
            return false;
        }

        // 8. Vincula os parâmetros de forma dinâmica
        $stmt->bind_param($types, ...$params);

        // 9. Executa e verifica o resultado
        if ($stmt->execute()) {
            return true; // Sucesso
        } else {
            // DEPURAÇÃO: Se a execução falhar, armazena o erro específico.
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
        // Prepara o UPDATE para os dados do usuário.
        $stmt = $this->conn->prepare("UPDATE usuario SET nome = ?, email = ?, cpf = ?, telefone = ?, permissao = ?, id_imobiliaria = ?, creci = ?, foto = ? WHERE id_usuario = ?");
        // Associa os parâmetros à instrução.
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
}
