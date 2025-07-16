<?php
class Tarefa
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    /**
     * Lista as tarefas, filtrando pela imobiliária do usuário logado.
     * O SuperAdmin vê todas as tarefas.
     */
    public function listar($id_imobiliaria_logada, $permissao_usuario)
    {
        $sql = "SELECT t.*, u.nome as nome_usuario, c.nome as nome_cliente
                FROM tarefas t
                LEFT JOIN usuario u ON t.id_usuario = u.id_usuario
                LEFT JOIN cliente c ON t.id_cliente = c.id_cliente";

        $params = [];
        $types = '';

        // Filtra por imobiliária, a menos que seja SuperAdmin
        if ($permissao_usuario !== 'SuperAdmin') {
            $sql .= " WHERE t.id_imobiliaria = ?";
            $params[] = $id_imobiliaria_logada;
            $types .= 'i';
        }

        $sql .= " ORDER BY t.prazo ASC, t.data_criacao DESC";

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Busca uma tarefa específica pelo ID.
     * Retorna todos os campos, incluindo id_imobiliaria para verificação de permissão.
     */
    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tarefas WHERE id_tarefa = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Cria uma nova tarefa no banco de dados.
     * Agora recebe um array de dados e salva o id_imobiliaria.
     */
    public function criar($dados)
    {
        // Adicione a coluna `id_imobiliaria INT NULL` na sua tabela `tarefas`.
        $stmt = $this->conn->prepare(
            "INSERT INTO tarefas (id_usuario, id_cliente, id_imobiliaria, descricao, status, prioridade, prazo) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "iiissss",
            $dados['id_usuario'],
            $dados['id_cliente'],
            $dados['id_imobiliaria'],
            $dados['descricao'],
            $dados['status'],
            $dados['prioridade'],
            $dados['prazo']
        );
        return $stmt->execute();
    }

    /**
     * Atualiza uma tarefa existente.
     * Agora recebe um array de dados.
     */
    public function atualizar($dados)
    {
        $stmt = $this->conn->prepare(
            "UPDATE tarefas SET id_usuario=?, id_cliente=?, id_imobiliaria=?, descricao=?, status=?, prioridade=?, prazo=? 
             WHERE id_tarefa=?"
        );
        $stmt->bind_param(
            "iiissssi",
            $dados['id_usuario'],
            $dados['id_cliente'],
            $dados['id_imobiliaria'],
            $dados['descricao'],
            $dados['status'],
            $dados['prioridade'],
            $dados['prazo'],
            $dados['id_tarefa']
        );
        return $stmt->execute();
    }

    /**
     * Exclui uma tarefa. A verificação de permissão é feita no Controller.
     */
    public function excluir($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM tarefas WHERE id_tarefa = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    public function listarPorPermissao($id_usuario, $id_imobiliaria, $permissao, $filtroUsuario = '', $filtroCliente = '')
    {
        $sql = "SELECT t.*, u.nome AS nome_usuario, c.nome AS nome_cliente
            FROM tarefas t
            LEFT JOIN usuario u ON t.id_usuario = u.id_usuario
            LEFT JOIN cliente c ON t.id_cliente = c.id_cliente
            WHERE 1=1";

        $params = [];
        $types = '';

        // Filtro por permissão
        if ($permissao === 'Admin' || $permissao === 'Coordenador') {
            $sql .= " AND t.id_imobiliaria = ?";
            $params[] = $id_imobiliaria;
            $types .= 'i';
        } elseif ($permissao === 'Corretor') {
            $sql .= " AND t.id_usuario = ?";
            $params[] = $id_usuario;
            $types .= 'i';
        }

        // Filtro por usuário (caso aplicável)
        if (!empty($filtroUsuario)) {
            $sql .= " AND t.id_usuario = ?";
            $params[] = $filtroUsuario;
            $types .= 'i';
        }

        // Filtro por cliente (caso aplicável)
        if (!empty($filtroCliente)) {
            $sql .= " AND t.id_cliente = ?";
            $params[] = $filtroCliente;
            $types .= 'i';
        }

        $sql .= " ORDER BY t.prazo ASC, t.data_criacao DESC";

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
}
