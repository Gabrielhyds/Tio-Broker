<?php

// Importa os Models usados pelo controller
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Interacao.php';
require_once __DIR__ . '/../models/DocumentoModel.php';
require_once __DIR__ . '/../config/validadores.php';
require_once __DIR__ . '/../config/rotas.php';

class ClienteController
{
    private $clienteModel;
    private $interacaoModel;
    private $documentoModel;
    private $db;
    private $dashboardBaseUrl;

    public function __construct($db)
    {
        $this->db = $db;
        $this->clienteModel = new Cliente($this->db);
        $this->interacaoModel = new Interacao($this->db);
        $this->documentoModel = new DocumentoModel($this->db);
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->dashboardBaseUrl = '../../index.php';
    }

    private function verificarLogin()
    {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['mensagem_erro'] = "Você precisa estar logado para acessar esta página.";
            header('Location: ' . BASE_URL . 'views/auth/login.php');
            exit;
        }
    }

    // ... (métodos listar, cadastrar, mostrar permanecem iguais)
    public function listar()
    {
        $this->verificarLogin();
        $dashboardUrl = $this->dashboardBaseUrl;
        $idImobiliaria = $_SESSION['usuario']['id_imobiliaria'] ?? null;
        $idUsuario = $_SESSION['usuario']['id_usuario'] ?? null;
        $isSuperAdmin = ($_SESSION['usuario']['permissao'] ?? '') === 'SuperAdmin';
        $clientes = $this->clienteModel->listar($idImobiliaria, $idUsuario, $isSuperAdmin);
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
                'renda' => !empty($_POST['renda']) ? (float)str_replace(['R$', '.', ','], ['', '', '.'], $_POST['renda']) : null,
                'entrada' => !empty($_POST['entrada']) ? (float)str_replace(['R$', '.', ','], ['', '', '.'], $_POST['entrada']) : null,
                'fgts' => !empty($_POST['fgts']) ? (float)str_replace(['R$', '.', ','], ['', '', '.'], $_POST['fgts']) : null,
                'subsidio' => !empty($_POST['subsidio']) ? (float)str_replace(['R$', '.', ','], ['', '', '.'], $_POST['subsidio']) : null,
                'foto' => $_POST['foto'] ?? null,
                'tipo_lista' => $_POST['tipo_lista'],
                'id_usuario' => $_SESSION['usuario']['id_usuario'],
                'id_imobiliaria' => $_SESSION['usuario']['id_imobiliaria'] ?? null
            ];
            if ($this->clienteModel->cadastrar($dados)) {
                $_SESSION['mensagem_sucesso'] = "Cliente cadastrado com sucesso!";
                header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=listar');
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
        if (!isset($_GET['id_cliente']) || !is_numeric($_GET['id_cliente'])) {
            $_SESSION['mensagem_erro'] = "ID do cliente inválido ou não fornecido.";
            header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=listar');
            exit;
        }
        $idCliente = (int)$_GET['id_cliente'];
        $cliente = $this->clienteModel->buscarPorId($idCliente);
        if (!$cliente) {
            $_SESSION['mensagem_erro'] = "Cliente não encontrado.";
            header('Location: . BASE_URL . views/contatos/index.php?controller=cliente&action=listar');
            exit;
        }
        $interacoes = $this->interacaoModel->listarPorCliente($idCliente);
        $documentos = $this->documentoModel->buscarPorCliente($idCliente);
        $activeMenu = 'contatos';
        $conteudo = __DIR__ . '/../views/contatos/mostrar_cliente.php';
        require_once __DIR__ . '/../views/layout/template_base.php';
    }

    /**
     * Edita os dados de um cliente existente
     * ESTA É A FUNÇÃO CORRIGIDA
     */
    public function editar()
    {
        $this->verificarLogin();

        if (!isset($_GET['id_cliente']) || !is_numeric($_GET['id_cliente'])) {
            $_SESSION['mensagem_erro'] = "ID do cliente inválido para edição.";
            header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=listar');
            exit;
        }

        $idCliente = (int)$_GET['id_cliente'];
        $urlRedirecionamentoEditar = BASE_URL . 'views/contatos/index.php?controller=cliente&action=editar&id_cliente=' . $idCliente;
        $urlRedirecionamentoMostrar = BASE_URL . 'views/contatos/index.php?controller=cliente&action=mostrar&id_cliente=' . $idCliente;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['nome']) || empty($_POST['numero']) || empty($_POST['cpf']) || empty($_POST['tipo_lista'])) {
                $_SESSION['mensagem_erro_form'] = "Nome, Número, CPF e Tipo de Lista são obrigatórios.";
                header('Location: ' . $urlRedirecionamentoEditar);
                exit;
            }

            if (!validarCpf($_POST['cpf'])) {
                $_SESSION['mensagem_erro_form'] = "CPF inválido. Verifique e tente novamente.";
                header('Location: ' . $urlRedirecionamentoEditar);
                exit;
            }
            
            // *** CORREÇÃO DEFINITIVA: VERIFICAÇÃO DE CPF DUPLICADO ***
            $cpfSubmetido = $_POST['cpf'];
            $clienteExistente = $this->clienteModel->buscarPorCpf($cpfSubmetido);

            // Se o CPF existe E pertence a um cliente DIFERENTE do que estamos editando
            if ($clienteExistente && $clienteExistente['id_cliente'] != $idCliente) {
                $_SESSION['mensagem_erro_form'] = "O CPF '{$cpfSubmetido}' já está em uso por outro cliente.";
                header('Location: ' . $urlRedirecionamentoEditar);
                exit;
            }
            
            $dadosAtualizar = [
                'nome' => $_POST['nome'],
                'numero' => $_POST['numero'],
                'cpf' => $cpfSubmetido, // Usa a variável já definida
                'empreendimento' => $_POST['empreendimento'] ?? null,
                'renda' => !empty($_POST['renda']) ? (float)str_replace(['R$', '.', ','], ['', '', '.'], $_POST['renda']) : null,
                'entrada' => !empty($_POST['entrada']) ? (float)str_replace(['R$', '.', ','], ['', '', '.'], $_POST['entrada']) : null,
                'fgts' => !empty($_POST['fgts']) ? (float)str_replace(['R$', '.', ','], ['', '', '.'], $_POST['fgts']) : null,
                'subsidio' => !empty($_POST['subsidio']) ? (float)str_replace(['R$', '.', ','], ['', '', '.'], $_POST['subsidio']) : null,
                'foto' => $_POST['foto'] ?? null,
                'tipo_lista' => $_POST['tipo_lista'],
            ];

            if ($this->clienteModel->atualizar($idCliente, $dadosAtualizar)) {
                $_SESSION['mensagem_sucesso'] = "Cliente atualizado com sucesso!";
                header('Location: ' . $urlRedirecionamentoMostrar);
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao atualizar cliente. Verifique os dados e tente novamente.";
                header('Location: ' . $urlRedirecionamentoEditar);
            }
            exit;
        } else {
            $cliente = $this->clienteModel->buscarPorId($idCliente);
            if (!$cliente) {
                $_SESSION['mensagem_erro'] = "Cliente não encontrado para edição.";
                header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=listar');
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
            header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=listar');
            exit;
        }
        $idCliente = (int)$_GET['id_cliente'];
        if ($this->clienteModel->excluir($idCliente)) {
            $_SESSION['mensagem_sucesso'] = "Cliente excluído com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao excluir cliente. Pode haver interações ou documentos associados.";
        }
        header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=listar');
        exit;
    }
}
