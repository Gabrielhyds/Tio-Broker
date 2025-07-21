<?php

// Importa os Models e arquivos de configuração
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

    private function handleFileUpload()
    {
        if (isset($_FILES['foto_arquivo']) && $_FILES['foto_arquivo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['foto_arquivo']['tmp_name'];
            $fileName = $_FILES['foto_arquivo']['name'];
            $fileSize = $_FILES['foto_arquivo']['size'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $allowedfileExtensions = ['jpg', 'gif', 'png', 'jpeg'];
            $maxFileSize = 2 * 1024 * 1024;

            if (in_array($fileExtension, $allowedfileExtensions) && $fileSize < $maxFileSize) {
                $uploadFileDir = __DIR__ . '/../uploads/fotos_clientes/';
                $dest_path = $uploadFileDir . $newFileName;
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0777, true);
                }
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    return 'uploads/fotos_clientes/' . $newFileName;
                }
            }
        }
        return null;
    }

    public function cadastrar()
    {
        $this->verificarLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // --- VALIDAÇÃO DOS DADOS DE ENTRADA ---
            if (!validarCpf($_POST['cpf'])) {
                $_SESSION['mensagem_erro_form'] = "CPF inválido. Por favor, verifique o número digitado.";
                // ALTERAÇÃO: Salva os dados do formulário na sessão antes de redirecionar
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=cadastrar');
                exit;
            }
            if (!validarTelefone($_POST['numero'])) {
                $_SESSION['mensagem_erro_form'] = "Número de telefone inválido.";
                // ALTERAÇÃO: Salva os dados do formulário na sessão antes de redirecionar
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=cadastrar');
                exit;
            }
            // --- FIM DA VALIDAÇÃO ---

            $caminhoFoto = $this->handleFileUpload();
            $dados = [
                'nome' => $_POST['nome'],
                'numero' => $_POST['numero'],
                'cpf' => $_POST['cpf'],
                'empreendimento' => $_POST['empreendimento'] ?? null,
                'renda' => !empty($_POST['renda']) ? (float)$_POST['renda'] : null,
                'entrada' => !empty($_POST['entrada']) ? (float)$_POST['entrada'] : null,
                'fgts' => !empty($_POST['fgts']) ? (float)$_POST['fgts'] : null,
                'subsidio' => !empty($_POST['subsidio']) ? (float)$_POST['subsidio'] : null,
                'foto' => $caminhoFoto,
                'tipo_lista' => $_POST['tipo_lista'],
                'id_usuario' => $_SESSION['usuario']['id_usuario'],
                'id_imobiliaria' => $_SESSION['usuario']['id_imobiliaria'] ?? null
            ];
            if ($this->clienteModel->cadastrar($dados)) {
                $_SESSION['mensagem_sucesso'] = "Cliente cadastrado com sucesso!";
                header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=listar');
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao cadastrar cliente.";
                if ($caminhoFoto && file_exists(__DIR__ . '/../' . $caminhoFoto)) {
                    unlink(__DIR__ . '/../' . $caminhoFoto);
                }
                // ALTERAÇÃO: Salva os dados do formulário na sessão em caso de erro no banco
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=cadastrar');
            }
            exit;
        }
        require __DIR__ . '/../views/contatos/cadastrar_cliente.php';
    }

    public function mostrar()
    {
        $this->verificarLogin();
        if (!isset($_GET['id_cliente']) || !is_numeric($_GET['id_cliente'])) {
            header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=listar');
            exit;
        }
        $idCliente = (int)$_GET['id_cliente'];
        $cliente = $this->clienteModel->buscarPorId($idCliente);
        if (!$cliente) {
            header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=listar');
            exit;
        }
        $interacoes = $this->interacaoModel->listarPorCliente($idCliente);
        $documentos = $this->documentoModel->buscarPorCliente($idCliente);
        $activeMenu = 'contatos';
        $conteudo = __DIR__ . '/../views/contatos/mostrar_cliente.php';
        require_once __DIR__ . '/../views/layout/template_base.php';
    }

    public function editar()
    {
        $this->verificarLogin();
        if (!isset($_GET['id_cliente']) || !is_numeric($_GET['id_cliente'])) {
            $_SESSION['mensagem_erro_lista'] = "ID do cliente inválido para edição.";
            header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=listar');
            exit;
        }
        $idCliente = (int)$_GET['id_cliente'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // --- VALIDAÇÃO DOS DADOS DE ENTRADA ---
            if (!validarCpf($_POST['cpf'])) {
                $_SESSION['mensagem_erro_form'] = "CPF inválido. Por favor, verifique o número digitado.";
                // ALTERAÇÃO: Salva os dados do formulário na sessão antes de redirecionar
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=editar&id_cliente=' . $idCliente);
                exit;
            }
            if (!validarTelefone($_POST['numero'])) {
                $_SESSION['mensagem_erro_form'] = "Número de telefone inválido.";
                // ALTERAÇÃO: Salva os dados do formulário na sessão antes de redirecionar
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=editar&id_cliente=' . $idCliente);
                exit;
            }
            // --- FIM DA VALIDAÇÃO ---

            $clienteAtual = $this->clienteModel->buscarPorId($idCliente);
            $caminhoFotoAntiga = $clienteAtual['foto'];
            $caminhoFotoFinal = $caminhoFotoAntiga;

            $novoCaminhoFoto = $this->handleFileUpload();
            if ($novoCaminhoFoto) {
                $caminhoFotoFinal = $novoCaminhoFoto;
                if ($caminhoFotoAntiga && file_exists(__DIR__ . '/../' . $caminhoFotoAntiga)) {
                    unlink(__DIR__ . '/../' . $caminhoFotoAntiga);
                }
            } elseif (isset($_POST['remover_foto_existente']) && $_POST['remover_foto_existente'] == '1') {
                if ($caminhoFotoAntiga && file_exists(__DIR__ . '/../' . $caminhoFotoAntiga)) {
                    unlink(__DIR__ . '/../' . $caminhoFotoAntiga);
                }
                $caminhoFotoFinal = null;
            }

            $dadosAtualizar = [
                'nome' => $_POST['nome'],
                'numero' => $_POST['numero'],
                'cpf' => $_POST['cpf'],
                'empreendimento' => $_POST['empreendimento'] ?? null,
                'renda' => !empty($_POST['renda']) ? (float)$_POST['renda'] : null,
                'entrada' => !empty($_POST['entrada']) ? (float)$_POST['entrada'] : null,
                'fgts' => !empty($_POST['fgts']) ? (float)$_POST['fgts'] : null,
                'subsidio' => !empty($_POST['subsidio']) ? (float)$_POST['subsidio'] : null,
                'foto' => $caminhoFotoFinal,
                'tipo_lista' => $_POST['tipo_lista'],
            ];

            if ($this->clienteModel->atualizar($idCliente, $dadosAtualizar)) {
                $_SESSION['mensagem_sucesso'] = "Cliente atualizado com sucesso!";
                header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=mostrar&id_cliente=' . $idCliente);
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao atualizar cliente.";
                // ALTERAÇÃO: Salva os dados do formulário na sessão em caso de erro no banco
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=editar&id_cliente=' . $idCliente);
            }
            exit;
        } else {
            $cliente = $this->clienteModel->buscarPorId($idCliente);
            if (!$cliente) {
                $_SESSION['mensagem_erro_lista'] = "Não foi possível carregar os dados do cliente para edição.";
                header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=listar');
                exit;
            }
            require __DIR__ . '/../views/contatos/editar_cliente.php';
        }
    }

    public function excluir()
    {
        $this->verificarLogin();
        $idCliente = (int)($_GET['id_cliente'] ?? 0);
        if ($idCliente > 0) {
            $cliente = $this->clienteModel->buscarPorId($idCliente);
            if ($cliente && !empty($cliente['foto']) && file_exists(__DIR__ . '/../' . $cliente['foto'])) {
                unlink(__DIR__ . '/../' . $cliente['foto']);
            }
            
            if ($this->clienteModel->excluir($idCliente)) {
                $_SESSION['mensagem_sucesso'] = "Cliente excluído com sucesso!";
            } else {
                $_SESSION['mensagem_erro_lista'] = "Erro ao excluir cliente.";
            }
        }
        header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=listar');
        exit;
    }
}
