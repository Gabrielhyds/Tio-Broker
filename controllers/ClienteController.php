<?php

require_once __DIR__ . '/../models/Cliente.php'; // Model de Cliente
require_once __DIR__ . '/../models/Interacao.php'; // Model de Interacao
require_once __DIR__ . '/../models/DocumentoModel.php'; // Incluir o Model de Documento
require_once __DIR__ . '/../config/validadores.php';

class ClienteController
{
    private $clienteModel;
    private $interacaoModel;
    private $documentoModel; // Adicionar propriedade para o DocumentoModel
    private $db; // Propriedade para armazenar a conexão com o banco
    private $dashboardBaseUrl;

    public function __construct($db)
    {
        $this->db = $db; // Armazenar a conexão com o banco
        $this->clienteModel = new Cliente($this->db);
        $this->interacaoModel = new Interacao($this->db);
        $this->documentoModel = new DocumentoModel($this->db); // Instanciar o DocumentoModel com a conexão

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->dashboardBaseUrl = '../../index.php'; // Ajuste conforme a estrutura do seu projeto
    }

    private function verificarLogin()
    {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['mensagem_erro'] = "Você precisa estar logado para acessar esta página.";
            // Ajuste o caminho para o seu roteador principal se necessário
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    public function listar()
    {
        $this->verificarLogin();
        $dashboardUrl = $this->dashboardBaseUrl;
        $idImobiliaria = $_SESSION['usuario']['id_imobiliaria'] ?? null;
        $idUsuario = $_SESSION['usuario']['id_usuario'] ?? null;
        $isSuperAdmin = ($_SESSION['usuario']['permissao'] ?? '') === 'SuperAdmin';

        $clientes = $this->clienteModel->listar($idImobiliaria, $idUsuario, $isSuperAdmin);

        // Caminho para a view de listagem
        require __DIR__ . '/../views/contatos/listar_clientes.php';
    }

    public function cadastrar()
    {
        $this->verificarLogin();
        $dashboardUrl = $this->dashboardBaseUrl;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['nome']) || empty($_POST['numero']) || empty($_POST['cpf']) || empty($_POST['tipo_lista'])) {
                $_SESSION['mensagem_erro_form'] = "Nome, Número, CPF e Tipo de Lista são obrigatórios.";
                $cliente = $_POST;
                require __DIR__ . '/../views/contatos/cadastrar_cliente.php';
                exit;
            }
            if (!validarCpf($_POST['cpf'])) {
                $_SESSION['mensagem_erro'] = "CPF inválido. Verifique e tente novamente.";
                $cliente = $_POST;
                require __DIR__ . '/../views/contatos/cadastrar_cliente.php';
                exit;
            }

            $dados = [
                'nome' => $_POST['nome'],
                'numero' => $_POST['numero'],
                'cpf' => $_POST['cpf'],
                'empreendimento' => $_POST['empreendimento'] ?? null,
                'renda' => !empty($_POST['renda']) ? str_replace(['.', ','], ['', '.'], $_POST['renda']) : null,
                'entrada' => !empty($_POST['entrada']) ? str_replace(['.', ','], ['', '.'], $_POST['entrada']) : null,
                'fgts' => !empty($_POST['fgts']) ? str_replace(['.', ','], ['', '.'], $_POST['fgts']) : null,
                'subsidio' => !empty($_POST['subsidio']) ? str_replace(['.', ','], ['', '.'], $_POST['subsidio']) : null,
                'foto' => $_POST['foto'] ?? null, // Considere como o upload de fotos será tratado
                'tipo_lista' => $_POST['tipo_lista'],
                'id_usuario' => $_SESSION['usuario']['id_usuario'],
                'id_imobiliaria' => $_SESSION['usuario']['id_imobiliaria'] ?? null // Garanta que id_imobiliaria exista na sessão
            ];

            if ($this->clienteModel->cadastrar($dados)) {
                $_SESSION['mensagem_sucesso'] = "Cliente cadastrado com sucesso!";
                header('Location: index.php?controller=cliente&action=listar');
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao cadastrar cliente. Verifique os dados e tente novamente.";
                $cliente = $dados;
                require __DIR__ . '/../views/contatos/cadastrar_cliente.php';
            }
            exit;
        }
        require __DIR__ . '/../views/contatos/cadastrar_cliente.php';
    }

    public function mostrar()
    {
        $this->verificarLogin();
        $dashboardUrl = $this->dashboardBaseUrl;

        if (!isset($_GET['id_cliente']) || !is_numeric($_GET['id_cliente'])) {
            $_SESSION['mensagem_erro'] = "ID do cliente inválido ou não fornecido.";
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }
        $idCliente = (int)$_GET['id_cliente'];
        $cliente = $this->clienteModel->buscarPorId($idCliente);

        if (!$cliente) {
            $_SESSION['mensagem_erro'] = "Cliente não encontrado.";
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }

        // Buscar o histórico de interações para este cliente
        $interacoes = $this->interacaoModel->listarPorCliente($idCliente);

        // Buscar os documentos para este cliente << --- ADICIONADO AQUI
        $documentos = $this->documentoModel->buscarPorCliente($idCliente);

        // Caminho para a view de mostrar cliente
        require __DIR__ . '/../views/contatos/mostrar_cliente.php';
    }

    public function editar()
    {
        $this->verificarLogin();
        $dashboardUrl = $this->dashboardBaseUrl;

        if (!isset($_GET['id_cliente']) || !is_numeric($_GET['id_cliente'])) {
            $_SESSION['mensagem_erro'] = "ID do cliente inválido para edição.";
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }
        $idCliente = (int)$_GET['id_cliente'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['nome']) || empty($_POST['numero']) || empty($_POST['cpf']) || empty($_POST['tipo_lista'])) {
                $_SESSION['mensagem_erro_form'] = "Nome, Número, CPF e Tipo de Lista são obrigatórios.";
                $cliente = $_POST;
                $cliente['id_cliente'] = $idCliente;
                require __DIR__ . '/../views/contatos/editar_cliente.php';
                exit;
            }
            if (!validarCpf($_POST['cpf'])) {
                $_SESSION['mensagem_erro'] = "CPF inválido. Verifique e tente novamente.";
                $cliente = $_POST;
                $cliente['id_cliente'] = $idCliente;
                require __DIR__ . '/../views/contatos/editar_cliente.php';
                exit;
            }

            $dadosAtualizar = [
                'nome' => $_POST['nome'],
                'numero' => $_POST['numero'],
                'cpf' => $_POST['cpf'],
                'empreendimento' => $_POST['empreendimento'] ?? null,
                'renda' => !empty($_POST['renda']) ? str_replace(['.', ','], ['', '.'], $_POST['renda']) : null,
                'entrada' => !empty($_POST['entrada']) ? str_replace(['.', ','], ['', '.'], $_POST['entrada']) : null,
                'fgts' => !empty($_POST['fgts']) ? str_replace(['.', ','], ['', '.'], $_POST['fgts']) : null,
                'subsidio' => !empty($_POST['subsidio']) ? str_replace(['.', ','], ['', '.'], $_POST['subsidio']) : null,
                'foto' => $_POST['foto'] ?? null, // Considere como o upload de fotos será tratado
                'tipo_lista' => $_POST['tipo_lista'],
            ];

            if ($this->clienteModel->atualizar($idCliente, $dadosAtualizar)) {
                $_SESSION['mensagem_sucesso'] = "Cliente atualizado com sucesso!";
                header('Location: index.php?controller=cliente&action=mostrar&id_cliente=' . $idCliente);
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao atualizar cliente. Verifique os dados e tente novamente.";
                $cliente = array_merge(['id_cliente' => $idCliente], $dadosAtualizar); // Mescla para manter o ID
                require __DIR__ . '/../views/contatos/editar_cliente.php';
            }
            exit;
        } else {
            $cliente = $this->clienteModel->buscarPorId($idCliente);
            if (!$cliente) {
                $_SESSION['mensagem_erro'] = "Cliente não encontrado para edição.";
                header('Location: index.php?controller=cliente&action=listar');
                exit;
            }
            require __DIR__ . '/../views/contatos/editar_cliente.php';
        }
    }

    public function excluir()
    {
        $this->verificarLogin();
        if (!isset($_GET['id_cliente']) || !is_numeric($_GET['id_cliente'])) {
            $_SESSION['mensagem_erro'] = "ID do cliente inválido.";
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }
        $idCliente = (int)$_GET['id_cliente'];

        // Adicionar lógica para verificar permissões de exclusão, se necessário
        // Ex: if ($_SESSION['usuario']['permissao'] !== 'SuperAdmin' && $cliente['id_usuario'] !== $_SESSION['usuario']['id_usuario']) { ... }

        if ($this->clienteModel->excluir($idCliente)) {
            $_SESSION['mensagem_sucesso'] = "Cliente excluído com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao excluir cliente. Pode haver interações ou documentos associados.";
        }
        header('Location: index.php?controller=cliente&action=listar');
        exit;
    }
}
