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
        $sql = "INSERT INTO cliente (nome, numero, cpf, empreendimento, renda, entrada, fgts, subsidio, foto, tipo_lista, id_usuario, id_imobiliaria)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
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
        return $stmt->execute();
    }

    public function listar($idImobiliaria, $idUsuario, $isSuperAdmin)
    {
        if ($isSuperAdmin) {
            $sql = "SELECT * FROM cliente";
            $stmt = $this->db->prepare($sql);
        } else {
            $sql = "SELECT * FROM cliente WHERE id_imobiliaria = ? AND id_usuario = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ii", $idImobiliaria, $idUsuario);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $clientes = [];

        while ($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }

        return $clientes;
    }
}
