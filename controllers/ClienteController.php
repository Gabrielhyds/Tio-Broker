<?php

require_once __DIR__ . '/../models/Cliente.php';

class ClienteController
{
    private $clienteModel;

    public function __construct($db)
    {
        $this->clienteModel = new Cliente($db);
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function verificarLogin() {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['mensagem_erro'] = "Você precisa estar logado para acessar esta página.";
            header('Location: index.php?controller=auth&action=login'); // Ajuste para sua rota de login
            exit;
        }
    }

    public function listar()
    {
        $this->verificarLogin();
        $idImobiliaria = $_SESSION['usuario']['id_imobiliaria'] ?? null;
        $idUsuario = $_SESSION['usuario']['id_usuario'] ?? null;
        $isSuperAdmin = ($_SESSION['usuario']['permissao'] ?? '') === 'SuperAdmin';
        $clientes = $this->clienteModel->listar($idImobiliaria, $idUsuario, $isSuperAdmin);
        require __DIR__ . '/../views/contatos/listar_clientes.php';
    }

    public function cadastrar()
    {
        $this->verificarLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['nome']) || empty($_POST['numero']) || empty($_POST['cpf']) || empty($_POST['tipo_lista'])) {
                $_SESSION['mensagem_erro_form'] = "Nome, Número, CPF e Tipo de Lista são obrigatórios.";
                // Para manter os dados no formulário em caso de erro:
                $cliente = $_POST; // Passa os dados do POST para a view
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
                // Para manter os dados no formulário em caso de erro de banco:
                $cliente = $dados; // Passa os dados que seriam inseridos para a view
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

        if (!isset($_GET['id_cliente']) || !is_numeric($_GET['id_cliente'])) {
             $_SESSION['mensagem_erro'] = "ID do cliente inválido para edição.";
             header('Location: index.php?controller=cliente&action=listar');
             exit;
        }
        $idCliente = (int)$_GET['id_cliente'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar dados do POST (similar ao cadastrar)
            if (empty($_POST['nome']) || empty($_POST['numero']) || empty($_POST['cpf']) || empty($_POST['tipo_lista'])) {
                $_SESSION['mensagem_erro_form'] = "Nome, Número, CPF e Tipo de Lista são obrigatórios.";
                // Recarregar o formulário com os dados e a mensagem de erro
                $cliente = $_POST; // Mantém os dados submetidos
                $cliente['id_cliente'] = $idCliente; // Garante que o ID está presente para o action do form
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
                // Não estamos atualizando id_usuario ou id_imobiliaria aqui.
                // Se for necessário, adicione-os e ajuste o método no Model.
            ];

            if ($this->clienteModel->atualizar($idCliente, $dadosAtualizar)) {
                $_SESSION['mensagem_sucesso'] = "Cliente atualizado com sucesso!";
                header('Location: index.php?controller=cliente&action=mostrar&id_cliente=' . $idCliente);
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao atualizar cliente. Verifique os dados e tente novamente.";
                // Recarregar o formulário com os dados e a mensagem de erro
                $cliente = $dadosAtualizar; // Mantém os dados que seriam atualizados
                $cliente['id_cliente'] = $idCliente;
                require __DIR__ . '/../views/contatos/editar_cliente.php';
            }
            exit;

        } else { // Método GET: Carregar dados para o formulário
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
        
        if ($this->clienteModel->excluir($idCliente)) {
            $_SESSION['mensagem_sucesso'] = "Cliente excluído com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao excluir cliente.";
        }
        header('Location: index.php?controller=cliente&action=listar');
        exit;
    }
}
