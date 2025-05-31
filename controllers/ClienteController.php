<?php

require_once __DIR__ . '/../models/Cliente.php';

class ClienteController
{
    private $clienteModel;
    private $dashboardBaseUrl; // Variável para armazenar o caminho para o index.php raiz

    public function __construct($db)
    {
        $this->clienteModel = new Cliente($db);
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Define o caminho base para o index.php que redireciona para os dashboards
        // Ajuste este caminho se a estrutura do seu projeto for diferente.
        // Assumindo que as views do controller de cliente estão em views/contatos/
        // e o index.php principal está dois níveis acima.
        $this->dashboardBaseUrl = '../../index.php';
    }

    private function verificarLogin() {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['mensagem_erro'] = "Você precisa estar logado para acessar esta página.";
            // Esta rota é relativa ao index.php que está executando o controller
            // Se o index.php do controller está em views/contatos/, e o controller de auth também é chamado por ele:
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    public function listar()
    {
        $this->verificarLogin();
        $dashboardUrl = $this->dashboardBaseUrl; // Torna a URL do dashboard disponível para a view
        $idImobiliaria = $_SESSION['usuario']['id_imobiliaria'] ?? null;
        $idUsuario = $_SESSION['usuario']['id_usuario'] ?? null;
        $isSuperAdmin = ($_SESSION['usuario']['permissao'] ?? '') === 'SuperAdmin';
        $clientes = $this->clienteModel->listar($idImobiliaria, $idUsuario, $isSuperAdmin);
        require __DIR__ . '/../views/contatos/listar_clientes.php';
    }

    public function cadastrar()
    {
        $this->verificarLogin();
        $dashboardUrl = $this->dashboardBaseUrl; // Torna a URL do dashboard disponível para a view
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['nome']) || empty($_POST['numero']) || empty($_POST['cpf']) || empty($_POST['tipo_lista'])) {
                $_SESSION['mensagem_erro_form'] = "Nome, Número, CPF e Tipo de Lista são obrigatórios.";
                $cliente = $_POST; 
                require __DIR__ . '/../views/contatos/cadastrar_cliente.php';
                exit;
            }
            
            $dados = [
                'nome' => $_POST['nome'],
                'numero' => $_POST['numero'],
                'cpf' => $_POST['cpf'],
                'empreendimento' => $_POST['empreendimento'] ?? null,
                'renda' => !empty($_POST['renda']) ? $_POST['renda'] : null,
                'entrada' => !empty($_POST['entrada']) ? $_POST['entrada'] : null,
                'fgts' => !empty($_POST['fgts']) ? $_POST['fgts'] : null,
                'subsidio' => !empty($_POST['subsidio']) ? $_POST['subsidio'] : null,
                'foto' => $_POST['foto'] ?? null,
                'tipo_lista' => $_POST['tipo_lista'],
                'id_usuario' => $_SESSION['usuario']['id_usuario'],
                'id_imobiliaria' => $_SESSION['usuario']['id_imobiliaria']
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
        $dashboardUrl = $this->dashboardBaseUrl; // Torna a URL do dashboard disponível para a view

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
        require __DIR__ . '/../views/contatos/mostrar_cliente.php';
    }
    
    public function editar() {
        $this->verificarLogin();
        $dashboardUrl = $this->dashboardBaseUrl; // Torna a URL do dashboard disponível para a view

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

            $dadosAtualizar = [
                'nome' => $_POST['nome'],
                'numero' => $_POST['numero'],
                'cpf' => $_POST['cpf'],
                'empreendimento' => $_POST['empreendimento'] ?? null,
                'renda' => !empty($_POST['renda']) ? $_POST['renda'] : null,
                'entrada' => !empty($_POST['entrada']) ? $_POST['entrada'] : null,
                'fgts' => !empty($_POST['fgts']) ? $_POST['fgts'] : null,
                'subsidio' => !empty($_POST['subsidio']) ? $_POST['subsidio'] : null,
                'foto' => $_POST['foto'] ?? null,
                'tipo_lista' => $_POST['tipo_lista'],
            ];

            if ($this->clienteModel->atualizar($idCliente, $dadosAtualizar)) {
                $_SESSION['mensagem_sucesso'] = "Cliente atualizado com sucesso!";
                header('Location: index.php?controller=cliente&action=mostrar&id_cliente=' . $idCliente);
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao atualizar cliente. Verifique os dados e tente novamente.";
                $cliente = $dadosAtualizar; 
                $cliente['id_cliente'] = $idCliente;
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
        // Não precisa de $dashboardUrl aqui pois redireciona diretamente para a lista
        if (!isset($_GET['id_cliente']) || !is_numeric($_GET['id_cliente'])) {
            $_SESSION['mensagem_erro'] = "ID do cliente inválido.";
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }
        $idCliente = (int)$_GET['id_cliente'];
        
        if ($this->clienteModel->excluir($idCliente)) {
            $_SESSION['mensagem_sucesso'] = "Cliente excluído com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao excluir cliente.";
        }
        header('Location: index.php?controller=cliente&action=listar');
        exit;
    }
}
