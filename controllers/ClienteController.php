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
        // Garante que o usuário está logado
        if (!isset($_SESSION['usuario'])) {
            // Redireciona para o login ou exibe um erro
            header('Location: index.php?controller=auth&action=login'); // Supondo que você tenha um controller de autenticação
            exit;
        }

        $idImobiliaria = $_SESSION['usuario']['id_imobiliaria'];
        $idUsuario = $_SESSION['usuario']['id_usuario'];
        $isSuperAdmin = $_SESSION['usuario']['permissao'] === 'SuperAdmin';

        $clientes = $this->clienteModel->listar($idImobiliaria, $idUsuario, $isSuperAdmin);
        require __DIR__ . '/../views/contatos/listar_clientes.php';
    }

    public function cadastrar()
    {
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

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

            if ($this->clienteModel->cadastrar($dados)) {
                $_SESSION['mensagem_sucesso'] = "Cliente cadastrado com sucesso!";
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao cadastrar cliente.";
            }
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }

        require __DIR__ . '/../views/contatos/cadastrar_cliente.php';
    }

    /**
     * Lida com a exclusão do cliente.
     */
    public function excluir()
    {
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?controller=auth&action=login'); // Ou sua página de login
            exit;
        }

        // Verifica se o id_cliente foi fornecido na requisição
        if (!isset($_GET['id_cliente']) || !is_numeric($_GET['id_cliente'])) {
            $_SESSION['mensagem_erro'] = "ID do cliente inválido.";
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }

        $idCliente = (int)$_GET['id_cliente'];
        $idUsuarioLogado = $_SESSION['usuario']['id_usuario'];
        $idImobiliariaLogada = $_SESSION['usuario']['id_imobiliaria'];
        $isSuperAdmin = $_SESSION['usuario']['permissao'] === 'SuperAdmin';

        // Busca o cliente para verificar propriedade/permissões antes de excluir
        $clienteParaExcluir = $this->clienteModel->buscarPorId($idCliente);

        if (!$clienteParaExcluir) {
            $_SESSION['mensagem_erro'] = "Cliente não encontrado.";
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }

        // Verificação de permissão:
        // 1. SuperAdmin pode excluir qualquer cliente.
        // 2. Um usuário regular pode excluir um cliente se ele for o proprietário (id_usuario corresponde)
        //    E o cliente pertencer à sua imobiliária (id_imobiliaria corresponde).




            if ($this->clienteModel->excluir($idCliente)) {
                $_SESSION['mensagem_sucesso'] = "Cliente excluído com sucesso!";
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao excluir cliente.";
            }
    

        header('Location: index.php?controller=cliente&action=listar');
        exit;
    }

}