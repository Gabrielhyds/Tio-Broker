<?php

// Importa os Models usados pelo controller
require_once __DIR__ . '/../models/Cliente.php';      // Model responsável pelos dados do cliente
require_once __DIR__ . '/../models/Interacao.php';    // Model para interações com o cliente
require_once __DIR__ . '/../models/DocumentoModel.php'; // Model para documentos do cliente
require_once __DIR__ . '/../config/validadores.php';  // Funções auxiliares de validação (ex: validarCpf)

class ClienteController
{
    private $clienteModel;
    private $interacaoModel;
    private $documentoModel;
    private $db; // Conexão com o banco de dados
    private $dashboardBaseUrl;

    // Construtor: instancia os models e inicia a sessão se necessário
    public function __construct($db)
    {
        $this->db = $db;

        // Instancia os Models com a conexão ao banco
        $this->clienteModel = new Cliente($this->db);
        $this->interacaoModel = new Interacao($this->db);
        $this->documentoModel = new DocumentoModel($this->db);

        // Inicia sessão, se ainda não estiver iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Caminho base para redirecionamentos
        $this->dashboardBaseUrl = '../../index.php';
    }

    // Verifica se o usuário está logado
    private function verificarLogin()
    {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['mensagem_erro'] = "Você precisa estar logado para acessar esta página.";
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    // Lista todos os clientes visíveis ao usuário
    public function listar()
    {
        $this->verificarLogin();

        $dashboardUrl = $this->dashboardBaseUrl;
        $idImobiliaria = $_SESSION['usuario']['id_imobiliaria'] ?? null;
        $idUsuario = $_SESSION['usuario']['id_usuario'] ?? null;
        $isSuperAdmin = ($_SESSION['usuario']['permissao'] ?? '') === 'SuperAdmin';

        // Busca os clientes com base no perfil do usuário
        $clientes = $this->clienteModel->listar($idImobiliaria, $idUsuario, $isSuperAdmin);

        // Carrega a view de listagem de clientes
        require __DIR__ . '/../views/contatos/listar_clientes.php';
    }

    // Cadastro de novo cliente
    public function cadastrar()
    {
        $this->verificarLogin();
        $dashboardUrl = $this->dashboardBaseUrl;

        // Se o formulário foi submetido
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Verifica campos obrigatórios
            if (empty($_POST['nome']) || empty($_POST['numero']) || empty($_POST['cpf']) || empty($_POST['tipo_lista'])) {
                $_SESSION['mensagem_erro_form'] = "Nome, Número, CPF e Tipo de Lista são obrigatórios.";
                $cliente = $_POST;
                require __DIR__ . '/../views/contatos/cadastrar_cliente.php';
                exit;
            }

            // Valida o CPF
            if (!validarCpf($_POST['cpf'])) {
                $_SESSION['mensagem_erro'] = "CPF inválido. Verifique e tente novamente.";
                $cliente = $_POST;
                require __DIR__ . '/../views/contatos/cadastrar_cliente.php';
                exit;
            }

            // Prepara os dados para inserção
            $dados = [
                'nome' => $_POST['nome'],
                'numero' => $_POST['numero'],
                'cpf' => $_POST['cpf'],
                'empreendimento' => $_POST['empreendimento'] ?? null,
                // ✅ CORREÇÃO: Converte os valores para float
                'renda' => !empty($_POST['renda']) ? (float)$_POST['renda'] : null,
                'entrada' => !empty($_POST['entrada']) ? (float)$_POST['entrada'] : null,
                'fgts' => !empty($_POST['fgts']) ? (float)$_POST['fgts'] : null,
                'subsidio' => !empty($_POST['subsidio']) ? (float)$_POST['subsidio'] : null,
                'foto' => $_POST['foto'] ?? null,
                'tipo_lista' => $_POST['tipo_lista'],
                'id_usuario' => $_SESSION['usuario']['id_usuario'],
                'id_imobiliaria' => $_SESSION['usuario']['id_imobiliaria'] ?? null
            ];

            // Tenta cadastrar o cliente
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

        // Se não houve envio de formulário, carrega a view do formulário
        require __DIR__ . '/../views/contatos/cadastrar_cliente.php';
    }

    // Exibe os detalhes de um cliente
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

        $interacoes = $this->interacaoModel->listarPorCliente($idCliente);
        $documentos = $this->documentoModel->buscarPorCliente($idCliente);

        require __DIR__ . '/../views/contatos/mostrar_cliente.php';
    }

    // Edita os dados de um cliente existente
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

            // Prepara os dados para atualização
            $dadosAtualizar = [
                'nome' => $_POST['nome'],
                'numero' => $_POST['numero'],
                'cpf' => $_POST['cpf'],
                'empreendimento' => $_POST['empreendimento'] ?? null,
                // ✅ CORREÇÃO: Converte os valores para float
                'renda' => !empty($_POST['renda']) ? (float)$_POST['renda'] : null,
                'entrada' => !empty($_POST['entrada']) ? (float)$_POST['entrada'] : null,
                'fgts' => !empty($_POST['fgts']) ? (float)$_POST['fgts'] : null,
                'subsidio' => !empty($_POST['subsidio']) ? (float)$_POST['subsidio'] : null,
                'foto' => $_POST['foto'] ?? null,
                'tipo_lista' => $_POST['tipo_lista'],
            ];

            // Atualiza o cliente
            if ($this->clienteModel->atualizar($idCliente, $dadosAtualizar)) {
                $_SESSION['mensagem_sucesso'] = "Cliente atualizado com sucesso!";
                header('Location: index.php?controller=cliente&action=mostrar&id_cliente=' . $idCliente);
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao atualizar cliente. Verifique os dados e tente novamente.";
                $cliente = array_merge(['id_cliente' => $idCliente], $dadosAtualizar);
                require __DIR__ . '/../views/contatos/editar_cliente.php';
            }
            exit;
        } else {
            // Se não for POST, busca os dados para preencher o formulário
            $cliente = $this->clienteModel->buscarPorId($idCliente);
            if (!$cliente) {
                $_SESSION['mensagem_erro'] = "Cliente não encontrado para edição.";
                header('Location: index.php?controller=cliente&action=listar');
                exit;
            }
            require __DIR__ . '/../views/contatos/editar_cliente.php';
        }
    }

    // Exclui um cliente
    public function excluir()
    {
        $this->verificarLogin();

        if (!isset($_GET['id_cliente']) || !is_numeric($_GET['id_cliente'])) {
            $_SESSION['mensagem_erro'] = "ID do cliente inválido.";
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }

        $idCliente = (int)$_GET['id_cliente'];

        if ($this->clienteModel->excluir($idCliente)) {
            $_SESSION['mensagem_sucesso'] = "Cliente excluído com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao excluir cliente. Pode haver interações ou documentos associados.";
        }

        header('Location: index.php?controller=cliente&action=listar');
        exit;
    }
}
