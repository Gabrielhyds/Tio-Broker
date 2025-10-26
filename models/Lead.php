<?php
class Lead {
    private $conn;
    private $table_name = "leads";
    private $table_interacoes = "lead_interacoes";

    // Construtor
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * RF01: Cadastrar Lead
     */
    public function cadastrar($dados) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nome, email, telefone, origem, interesse, id_usuario_responsavel, id_imobiliaria) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        // Limpa os dados
        $nome = htmlspecialchars(strip_tags($dados['nome']));
        $email = htmlspecialchars(strip_tags($dados['email']));
        $telefone = htmlspecialchars(strip_tags($dados['telefone']));
        $origem = htmlspecialchars(strip_tags($dados['origem']));
        $interesse = htmlspecialchars(strip_tags($dados['interesse']));
        $id_usuario = (int)$dados['id_usuario_responsavel'];
        $id_imobiliaria = (int)$dados['id_imobiliaria'];

        $stmt->bind_param("sssssii", $nome, $email, $telefone, $origem, $interesse, $id_usuario, $id_imobiliaria);

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }

    /**
     * RF02: Editar Lead
     */
    public function editar($id, $dados) {
        $query = "UPDATE " . $this->table_name . " SET 
                    nome = ?, email = ?, telefone = ?, origem = ?, 
                    interesse = ?, id_usuario_responsavel = ?
                  WHERE id_lead = ?";

        $stmt = $this->conn->prepare($query);

        $nome = htmlspecialchars(strip_tags($dados['nome']));
        $email = htmlspecialchars(strip_tags($dados['email']));
        $telefone = htmlspecialchars(strip_tags($dados['telefone']));
        $origem = htmlspecialchars(strip_tags($dados['origem']));
        $interesse = htmlspecialchars(strip_tags($dados['interesse']));
        $id_usuario = (int)$dados['id_usuario_responsavel'];
        $id_lead = (int)$id;

        $stmt->bind_param("sssssii", $nome, $email, $telefone, $origem, $interesse, $id_usuario, $id_lead);

        return $stmt->execute();
    }

    /**
     * RF03: Excluir Lead (Exclusão Lógica)
     */
    public function excluir($id) {
        // Validação (RF03 Erro): Verificar negociações abertas (Simplificado)
        // Em um app real, verificaríamos outra tabela. Aqui, vamos apenas inativar.
        
        $query = "UPDATE " . $this->table_name . " SET is_deleted = 1 WHERE id_lead = ?";
        $stmt = $this->conn->prepare($query);
        $id_lead = (int)$id;
        $stmt->bind_param("i", $id_lead);
        
        return $stmt->execute();
    }

    /**
     * RF04: Visualizar Pipeline / Listar Todos
     * RF07: Buscar e Filtrar Leads (Simplificado)
     */
    public function listarTodos($filtros = []) {
        $query = "SELECT l.*, u.nome as nome_responsavel 
                  FROM " . $this->table_name . " l
                  LEFT JOIN usuario u ON l.id_usuario_responsavel = u.id_usuario
                  WHERE l.is_deleted = 0";
        
        // RF07: Filtro de busca (simples)
        if (!empty($filtros['termo'])) {
            $termo = "%" . $this_conn->real_escape_string($filtros['termo']) . "%";
            $query .= " AND (l.nome LIKE '$termo' OR l.email LIKE '$termo' OR l.telefone LIKE '$termo')";
        }

        // Não usamos ORDER BY para performance, ordenamos no PHP se necessário
        
        $result = $this->conn->query($query);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    /**
     * RF05: Movimentar Lead no Pipeline
     */
    public function moverPipeline($id, $novoStatus) {
        $query = "UPDATE " . $this->table_name . " SET status_pipeline = ? WHERE id_lead = ?";
        $stmt = $this->conn->prepare($query);
        
        $id_lead = (int)$id;
        $status = htmlspecialchars(strip_tags($novoStatus));
        
        $stmt->bind_param("si", $status, $id_lead);
        return $stmt->execute();
    }

    /**
     * RF06: Atribuir Lead a Usuários
     */
    public function atribuirResponsavel($id, $id_usuario) {
        $query = "UPDATE " . $this->table_name . " SET id_usuario_responsavel = ? WHERE id_lead = ?";
        $stmt = $this->conn->prepare($query);
        
        $id_lead = (int)$id;
        $id_usr = (int)$id_usuario;
        
        $stmt->bind_param("ii", $id_usr, $id_lead);
        return $stmt->execute();
    }

    /**
     * RF08: Registrar Interações do Lead
     */
    public function registrarInteracao($dados) {
        $query = "INSERT INTO " . $this->table_interacoes . "
                  (id_lead, id_usuario, tipo_interacao, descricao) 
                  VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);

        $id_lead = (int)$dados['id_lead'];
        $id_usuario = (int)$dados['id_usuario'];
        $tipo = htmlspecialchars(strip_tags($dados['tipo_interacao']));
        $descricao = htmlspecialchars(strip_tags($dados['descricao']));
        
        $stmt->bind_param("iiss", $id_lead, $id_usuario, $tipo, $descricao);
        return $stmt->execute();
    }

    /**
     * Busca um lead específico e suas interações (RF02, RF08)
     */
    public function buscarPorId($id) {
        $lead = [];
        $id_lead = (int)$id;

        // 1. Buscar dados do Lead
        $query_lead = "SELECT l.*, u.nome as nome_responsavel 
                       FROM " . $this->table_name . " l
                       LEFT JOIN usuario u ON l.id_usuario_responsavel = u.id_usuario
                       WHERE l.id_lead = ? AND l.is_deleted = 0";
        
        $stmt_lead = $this->conn->prepare($query_lead);
        $stmt_lead->bind_param("i", $id_lead);
        $stmt_lead->execute();
        $result_lead = $stmt_lead->get_result();
        
        if ($result_lead->num_rows > 0) {
            $lead['dados'] = $result_lead->fetch_assoc();
        } else {
            return false; // Lead não encontrado
        }

        // 2. Buscar Interações (RF08)
        $query_int = "SELECT i.*, u.nome as nome_usuario 
                      FROM " . $this->table_interacoes . " i
                      JOIN usuario u ON i.id_usuario = u.id_usuario
                      WHERE i.id_lead = ? 
                      ORDER BY i.data_interacao DESC"; // JS cuidará da ordenação

        $stmt_int = $this->conn->prepare($query_int);
        $stmt_int->bind_param("i", $id_lead);
        $stmt_int->execute();
        $result_int = $stmt_int->get_result();
        
        $lead['interacoes'] = $result_int->fetch_all(MYSQLI_ASSOC);

        return $lead;
    }
}
?>
