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
        $dados['foto'] = $dados['foto'] ?? null;
        $dados['empreendimento'] = $dados['empreendimento'] ?? null;
        // CPF é NOT NULL na tabela cliente, validação deve garantir que não seja nulo.
        // $dados['cpf'] = $dados['cpf'] ?? null; // Remover ou garantir que sempre venha do POST

        $dados['renda'] = !empty($dados['renda']) ? $dados['renda'] : null;
        $dados['entrada'] = !empty($dados['entrada']) ? $dados['entrada'] : null;
        $dados['fgts'] = !empty($dados['fgts']) ? $dados['fgts'] : null;
        $dados['subsidio'] = !empty($dados['subsidio']) ? $dados['subsidio'] : null;


        // Assume-se que a coluna 'criado_em' existe e é gerenciada pelo banco (DEFAULT CURRENT_TIMESTAMP)
        // ou será adicionada na query de INSERT se necessário.
        // Se for adicionar via PHP, use NOW() como no exemplo abaixo.
        $sql = "INSERT INTO cliente (nome, numero, cpf, empreendimento, renda, entrada, fgts, subsidio, foto, tipo_lista, id_usuario, id_imobiliaria, criado_em)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Falha no prepare (cadastrar): (" . $this->db->errno . ") " . $this->db->error);
            return false;
        }
        $stmt->bind_param(
            "ssssddddssii",
            $dados['nome'],
            $dados['numero'],
            $dados['cpf'],
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

    public function listar($idImobiliaria = null, $idUsuario = null, $isSuperAdmin = false)
    {
        $clientes = [];
        $orderBy = "ORDER BY c.nome ASC"; // Ordenando por nome como padrão

        $sql = "SELECT c.*, u.nome as nome_corretor, im.nome as nome_imobiliaria
                FROM cliente c 
                LEFT JOIN usuario u ON c.id_usuario = u.id_usuario 
                LEFT JOIN imobiliaria im ON c.id_imobiliaria = im.id_imobiliaria
                {$orderBy}";
        
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
             error_log("Falha no prepare (listar todos os clientes): (" . $this->db->errno . ") " . $this->db->error . " SQL: " . $sql);
             return $clientes;
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

    public function buscarPorId($idCliente)
    {
        $sql = "SELECT c.*, u.nome as nome_corretor, u.email as email_corretor, u.telefone as telefone_corretor, 
                       im.nome as nome_imobiliaria
                FROM cliente c
                LEFT JOIN usuario u ON c.id_usuario = u.id_usuario
                LEFT JOIN imobiliaria im ON c.id_imobiliaria = im.id_imobiliaria
                WHERE c.id_cliente = ?";
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
     * Atualiza os dados de um cliente existente no banco de dados.
     * @param int $idCliente O ID do cliente a ser atualizado.
     * @param array $dados Os novos dados do cliente.
     * @return bool True em sucesso, false em falha.
     */
    public function atualizar($idCliente, $dados)
    {
        // Campos que podem ser atualizados
        // Não incluímos id_usuario, id_imobiliaria, criado_em pois geralmente não são alterados neste contexto.
        // Se precisar permitir alteração de corretor/imobiliária, adicione-os aqui e no formulário.
        $sql = "UPDATE cliente SET 
                    nome = ?, 
                    numero = ?, 
                    cpf = ?, 
                    empreendimento = ?, 
                    renda = ?, 
                    entrada = ?, 
                    fgts = ?, 
                    subsidio = ?, 
                    foto = ?, 
                    tipo_lista = ?
                WHERE id_cliente = ?";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Falha no prepare (atualizar): (" . $this->db->errno . ") " . $this->db->error . " SQL: " . $sql);
            return false;
        }

        // Certifica que os valores numéricos opcionais sejam NULL se vazios, ou o valor numérico.
        $renda = !empty($dados['renda']) ? (float)$dados['renda'] : null;
        $entrada = !empty($dados['entrada']) ? (float)$dados['entrada'] : null;
        $fgts = !empty($dados['fgts']) ? (float)$dados['fgts'] : null;
        $subsidio = !empty($dados['subsidio']) ? (float)$dados['subsidio'] : null;
        $foto = $dados['foto'] ?? null;
        $empreendimento = $dados['empreendimento'] ?? null;


        $stmt->bind_param(
            "ssssddddssi", // s: string, d: double, i: integer
            $dados['nome'],
            $dados['numero'],
            $dados['cpf'],
            $empreendimento,
            $renda,
            $entrada,
            $fgts,
            $subsidio,
            $foto,
            $dados['tipo_lista'],
            $idCliente
        );

        $success = $stmt->execute();
        if (!$success) {
            error_log("Falha na execução (atualizar): (" . $stmt->errno . ") " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }

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
