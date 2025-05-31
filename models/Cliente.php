<?php

class Cliente
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function cadastrar($dados)
    {
        // É uma boa prática garantir que todas as chaves esperadas existam.
        // Atenção: A coluna 'cpf' na tabela 'cliente' é NOT NULL. 
        // A lógica de validação deve garantir que $_POST['cpf'] sempre tenha um valor.
        $dados['foto'] = $dados['foto'] ?? null;
        $dados['empreendimento'] = $dados['empreendimento'] ?? null;
        // Se o CPF é NOT NULL no banco, ele não deveria ser ?? null aqui sem validação prévia.
        // $dados['cpf'] = $dados['cpf'] ?? null; // Esta linha pode causar erro se o CPF não for enviado.
                                                // O formulário e o controller devem garantir que o CPF seja enviado.

        $dados['renda'] = !empty($dados['renda']) ? $dados['renda'] : null;
        $dados['entrada'] = !empty($dados['entrada']) ? $dados['entrada'] : null;
        $dados['fgts'] = !empty($dados['fgts']) ? $dados['fgts'] : null;
        $dados['subsidio'] = !empty($dados['subsidio']) ? $dados['subsidio'] : null;


        $sql = "INSERT INTO cliente (nome, numero, cpf, empreendimento, renda, entrada, fgts, subsidio, foto, tipo_lista, id_usuario, id_imobiliaria)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            // Lidar com erro de prepare, ex: logar ou lançar exceção
            error_log("Falha no prepare (cadastrar): (" . $this->db->errno . ") " . $this->db->error);
            return false;
        }
        $stmt->bind_param(
            "ssssddddssii", // s para string, d para double/decimal, i para integer
            $dados['nome'],
            $dados['numero'],
            $dados['cpf'], // Deve ser garantido que $dados['cpf'] não é null aqui
            $dados['empreendimento'],
            $dados['renda'],
            $dados['entrada'],
            $dados['fgts'],
            $dados['subsidio'],
            $dados['foto'],
            $dados['tipo_lista'],
            $dados['id_usuario'],
            $dados['id_imobiliaria']
        );
        $success = $stmt->execute();
        if (!$success) {
            error_log("Falha na execução (cadastrar): (" . $stmt->errno . ") " . $stmt->error . " SQL: " . $sql);
        }
        $stmt->close();
        return $success;
    }

    /**
     * Lista todos os clientes para todos os tipos de usuário.
     * A diferenciação por $idImobiliaria e $isSuperAdmin foi removida para esta funcionalidade.
     */
    public function listar($idImobiliaria = null, $idUsuario = null, $isSuperAdmin = false) // Parâmetros mantidos por compatibilidade, mas não usados para filtrar
    {
        $clientes = [];
        // Todos os usuários visualizam todos os clientes.
        // A ordenação por 'criado_em' foi removida anteriormente pois a coluna não existia.
        // Se a coluna 'criado_em' existir, você pode adicionar 'ORDER BY c.criado_em DESC'
        // Exemplo: ALTER TABLE cliente ADD COLUMN criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
        $sql = "SELECT c.*, u.nome as nome_corretor, im.nome as nome_imobiliaria
                FROM cliente c 
                LEFT JOIN usuario u ON c.id_usuario = u.id_usuario 
                LEFT JOIN imobiliaria im ON c.id_imobiliaria = im.id_imobiliaria";
        
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
             error_log("Falha no prepare (listar todos os clientes): (" . $this->db->errno . ") " . $this->db->error . " SQL: " . $sql);
             return $clientes; // Retorna array vazio em caso de falha no prepare
        }

        if(!$stmt->execute()){
            error_log("Falha na execução (listar todos os clientes): (" . $stmt->errno . ") " . $stmt->error);
            $stmt->close();
            return $clientes;
        }
        
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }
        $stmt->close();
        return $clientes;
    }

    /**
     * Busca um único cliente pelo seu ID.
     * Usado para verificações de permissão antes de excluir ou para editar.
     * @param int $idCliente
     * @return array|null Os dados do cliente ou null se não encontrado.
     */
    public function buscarPorId($idCliente)
    {
        $sql = "SELECT id_cliente, nome, id_usuario, id_imobiliaria FROM cliente WHERE id_cliente = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Falha no prepare em buscarPorId: (" . $this->db->errno . ") " . $this->db->error . " SQL: " . $sql);
            return null;
        }
        $stmt->bind_param("i", $idCliente);
        
        if(!$stmt->execute()){
            error_log("Falha na execução (buscarPorId): (" . $stmt->errno . ") " . $stmt->error);
            $stmt->close();
            return null;
        }

        $result = $stmt->get_result();
        $cliente = $result->fetch_assoc();
        $stmt->close();
        return $cliente;
    }

    /**
     * Exclui um cliente do banco de dados.
     * @param int $idCliente O ID do cliente a ser excluído.
     * @return bool True em sucesso, false em falha.
     */
    public function excluir($idCliente)
    {
        $sql = "DELETE FROM cliente WHERE id_cliente = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Falha no prepare em excluir: (" . $this->db->errno . ") " . $this->db->error . " SQL: " . $sql);
            return false;
        }
        $stmt->bind_param("i", $idCliente);
        $success = $stmt->execute();
        if (!$success) {
             error_log("Falha na execução (excluir): (" . $stmt->errno . ") " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }
}
