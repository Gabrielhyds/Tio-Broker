<?php

class Cliente
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * NOVO MÉTODO: Busca um cliente pelo CPF.
     * @param string $cpf O CPF a ser pesquisado.
     * @return array|null Retorna os dados do cliente se encontrado, caso contrário null.
     */
    public function buscarPorCpf($cpf)
    {
        $sql = "SELECT id_cliente, cpf FROM cliente WHERE cpf = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Falha no prepare (buscarPorCpf): " . $this->db->error);
            return null;
        }
        $stmt->bind_param("s", $cpf);
        $stmt->execute();
        $result = $stmt->get_result();
        $cliente = $result->fetch_assoc();
        $stmt->close();
        return $cliente;
    }

    public function cadastrar($dados)
    {
        $dados['foto'] = $dados['foto'] ?? null;
        $dados['empreendimento'] = $dados['empreendimento'] ?? null;
        $renda = !empty($dados['renda']) ? (float)$dados['renda'] : 0.0;
        $entrada = !empty($dados['entrada']) ? (float)$dados['entrada'] : 0.0;
        $fgts = !empty($dados['fgts']) ? (float)$dados['fgts'] : 0.0;
        $subsidio = !empty($dados['subsidio']) ? (float)$dados['subsidio'] : 0.0;
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
            $renda,
            $entrada,
            $fgts,
            $subsidio,
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
        $orderBy = "ORDER BY c.nome ASC";
        $sql = "SELECT c.*, u.nome as nome_corretor, im.nome as nome_imobiliaria
                FROM cliente c 
                LEFT JOIN usuario u ON c.id_usuario = u.id_usuario 
                LEFT JOIN imobiliaria im ON c.id_imobiliaria = im.id_imobiliaria
                {$orderBy}";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Falha no prepare (listar): (" . $this->db->errno . ") " . $this->db->error);
            return $clientes;
        }
        if (!$stmt->execute()) {
            error_log("Falha na execução (listar): (" . $stmt->errno . ") " . $stmt->error);
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
            error_log("Falha no prepare (buscarPorId): (" . $this->db->errno . ") " . $this->db->error);
            return null;
        }
        $stmt->bind_param("i", $idCliente);
        if (!$stmt->execute()) {
            error_log("Falha na execução (buscarPorId): (" . $stmt->errno . ") " . $stmt->error);
            $stmt->close();
            return null;
        }
        $result = $stmt->get_result();
        $cliente = $result->fetch_assoc();
        $stmt->close();
        return $cliente;
    }

    public function atualizar($idCliente, $dados)
    {
        $sql = "UPDATE cliente SET nome = ?, numero = ?, cpf = ?, empreendimento = ?, renda = ?, entrada = ?, fgts = ?, subsidio = ?, foto = ?, tipo_lista = ?
                WHERE id_cliente = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Falha no prepare (atualizar): (" . $this->db->errno . ") " . $this->db->error);
            return false;
        }
        $renda = !empty($dados['renda']) ? (float)$dados['renda'] : 0.0;
        $entrada = !empty($dados['entrada']) ? (float)$dados['entrada'] : 0.0;
        $fgts = !empty($dados['fgts']) ? (float)$dados['fgts'] : 0.0;
        $subsidio = !empty($dados['subsidio']) ? (float)$dados['subsidio'] : 0.0;
        $foto = $dados['foto'] ?? null;
        $empreendimento = $dados['empreendimento'] ?? null;
        $stmt->bind_param(
            "ssssddddssi",
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
            error_log("Falha no prepare (excluir): (" . $this->db->errno . ") " . $this->db->error);
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

    public function listarTodos()
    {
        $clientes = [];
        $sql = "SELECT id_cliente, nome FROM cliente ORDER BY nome";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $clientes[] = $row;
            }
        }
        return $clientes;
    }
}
