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
     */
    public function listar($id_imobiliaria_logada, $permissao_usuario)
    {
        $sql = "SELECT t.*, u.nome as nome_usuario, c.nome as nome_cliente
                FROM tarefas t
                LEFT JOIN usuario u ON t.id_usuario = u.id_usuario
                LEFT JOIN cliente c ON t.id_cliente = c.id_cliente";

        $params = [];
        $types = '';

        if ($permissao_usuario !== 'SuperAdmin') {
            $sql .= " WHERE t.id_imobiliaria = ?";
            $params[] = $id_imobiliaria_logada;
            $types .= 'i';
        }

        $sql .= " ORDER BY t.prazo ASC, t.data_criacao DESC";

        $stmt = $this->conn->prepare($sql);
        
        // --- CORREÇÃO ---
        if ($stmt === false) {
            return [];
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Busca uma tarefa específica pelo ID.
     */
    public function buscarPorId($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tarefas WHERE id_tarefa = ?");
        
        // --- CORREÇÃO ---
        if ($stmt === false) {
            return null;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_assoc() : null;
    }

    /**
     * Cria uma nova tarefa no banco de dados.
     */
    public function criar($dados)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO tarefas (id_usuario, id_cliente, id_imobiliaria, descricao, status, prioridade, prazo) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        // --- CORREÇÃO ---
        if ($stmt === false) {
            return false;
        }

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
     */
    public function atualizar($dados)
    {
        $stmt = $this->conn->prepare(
            "UPDATE tarefas SET id_usuario=?, id_cliente=?, id_imobiliaria=?, descricao=?, status=?, prioridade=?, prazo=? 
             WHERE id_tarefa=?"
        );

        // --- CORREÇÃO ---
        if ($stmt === false) {
            return false;
        }

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
     * Exclui uma tarefa.
     */
    public function excluir($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM tarefas WHERE id_tarefa = ?");

        // --- CORREÇÃO ---
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Lista tarefas com base na permissão do usuário e filtros.
     */
    public function listarPorPermissao($id_usuario, $id_imobiliaria, $permissao, $filtroUsuario = '', $filtroCliente = '')
    {
        $sql = "SELECT t.*, u.nome AS nome_usuario, c.nome AS nome_cliente
                FROM tarefas t
                LEFT JOIN usuario u ON t.id_usuario = u.id_usuario
                LEFT JOIN cliente c ON t.id_cliente = c.id_cliente
                WHERE 1=1";

        $params = [];
        $types = '';

        if ($permissao === 'Admin' || $permissao === 'Coordenador') {
            $sql .= " AND t.id_imobiliaria = ?";
            $params[] = $id_imobiliaria;
            $types .= 'i';
        } elseif ($permissao === 'Corretor') {
            $sql .= " AND t.id_usuario = ?";
            $params[] = $id_usuario;
            $types .= 'i';
        }

        if (!empty($filtroUsuario)) {
            $sql .= " AND t.id_usuario = ?";
            $params[] = $filtroUsuario;
            $types .= 'i';
        }

        if (!empty($filtroCliente)) {
            $sql .= " AND t.id_cliente = ?";
            $params[] = $filtroCliente;
            $types .= 'i';
        }

        $sql .= " ORDER BY t.prazo ASC, t.data_criacao DESC";

        $stmt = $this->conn->prepare($sql);
        
        // --- CORREÇÃO ---
        if ($stmt === false) {
            return [];
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
}
