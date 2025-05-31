<?php

require_once __DIR__ . '/../models/Cliente.php';

class ClienteController
{
    private $clienteModel;

    public function __construct($db)
    {
        $this->clienteModel = new Cliente($db);
    }

    public function listar()
    {
        session_start();
        $idImobiliaria = $_SESSION['usuario']['id_imobiliaria'];
        $idUsuario = $_SESSION['usuario']['id_usuario'];
        $isSuperAdmin = $_SESSION['usuario']['permissao'] === 'SuperAdmin';

        $clientes = $this->clienteModel->listar($idImobiliaria, $idUsuario, $isSuperAdmin);
         require __DIR__ . '/../views/contatos/listar_clientes.php';
    }

    public function cadastrar()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'nome' => $_POST['nome'],
                'numero' => $_POST['numero'],
                'cpf' => $_POST['cpf'],
                'empreendimento' => $_POST['empreendimento'],
                'renda' => $_POST['renda'],
                'entrada' => $_POST['entrada'],
                'fgts' => $_POST['fgts'],
                'subsidio' => $_POST['subsidio'],
                'foto' => $_POST['foto'] ?? null,
                'tipo_lista' => $_POST['tipo_lista'],
                'id_usuario' => $_SESSION['usuario']['id_usuario'],
                'id_imobiliaria' => $_SESSION['usuario']['id_imobiliaria']
            ];

            $this->clienteModel->cadastrar($dados);
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }

        require __DIR__ . "../views/contatos/cadastrar_cliente.php";
    }
}
