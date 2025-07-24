<?php

// Importa os Models e arquivos de configuração necessários
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Interacao.php';
require_once __DIR__ . '/../models/DocumentoModel.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../config/validadores.php';
require_once __DIR__ . '/../config/rotas.php';

class ClienteController
{
    private $clienteModel;
    private $interacaoModel;
    private $documentoModel;
    private $usuarioModel;
    private $db;
    private $dashboardBaseUrl;

    public function __construct($db)
    {
        $this->db = $db;
        $this->clienteModel = new Cliente($this->db);
        $this->interacaoModel = new Interacao($this->db);
        $this->documentoModel = new DocumentoModel($this->db);
        $this->usuarioModel = new Usuario($this->db);

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->dashboardBaseUrl = '../../index.php';
    }

    // Garante que o usuário esteja autenticado
    private function verificarLogin()
    {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['mensagem_erro'] = "Você precisa estar logado para acessar esta página.";
            header('Location: ' . BASE_URL . 'views/auth/login.php');
            exit;
        }
    }

    // Lista todos os clientes com base na permissão do usuário
    public function listar()
    {
        $this->verificarLogin();

        $idImobiliaria = $_SESSION['usuario']['id_imobiliaria'] ?? null;
        $idUsuario = $_SESSION['usuario']['id_usuario'] ?? null;
        $permissao = $_SESSION['usuario']['permissao'] ?? '';
        $isSuperAdmin = $permissao === 'SuperAdmin';

        $filtroCorretor = isset($_GET['filtro_corretor']) && is_numeric($_GET['filtro_corretor']) ? (int)$_GET['filtro_corretor'] : null;

        $clientes = $this->clienteModel->listar($idImobiliaria, $idUsuario, $isSuperAdmin, $permissao, $filtroCorretor);

        $corretoresFiltro = [];
        if (in_array($permissao, ['Admin', 'Coordenador', 'SuperAdmin'])) {
            $idImobiliariaParaFiltro = $isSuperAdmin ? null : $idImobiliaria;
            $corretoresFiltro = $this->usuarioModel->listarPorImobiliaria($idImobiliariaParaFiltro);
        }

        require __DIR__ . '/../views/contatos/listar_clientes.php';
    }

    // Processa upload de imagem do cliente (foto de perfil)
    private function handleFileUpload()
    {
        if (isset($_FILES['foto_arquivo']) && $_FILES['foto_arquivo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['foto_arquivo']['tmp_name'];
            $fileName = $_FILES['foto_arquivo']['name'];
            $fileSize = $_FILES['foto_arquivo']['size'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $allowedfileExtensions = ['jpg', 'gif', 'png', 'jpeg'];
            $maxFileSize = 2 * 1024 * 1024; // 2MB

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

    // Exibe o formulário de cadastro e processa os dados enviados
    public function cadastrar()
    {
        $this->verificarLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validarCpf($_POST['cpf'])) {
                $_SESSION['mensagem_erro_form'] = "CPF inválido.";
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=cadastrar');
                exit;
            }

            if (!validarTelefone($_POST['numero'])) {
                $_SESSION['mensagem_erro_form'] = "Número de telefone inválido.";
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=cadastrar');
                exit;
            }

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
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=cadastrar');
            }

            exit;
        }

        require __DIR__ . '/../views/contatos/cadastrar_cliente.php';
    }

    // Exibe os detalhes de um cliente específico
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

    // Exibe formulário de edição e processa atualizações
    public function editar()
    {
        $this->verificarLogin();

        $idCliente = (int)($_GET['id_cliente'] ?? 0);
        if ($idCliente <= 0) {
            $_SESSION['mensagem_erro_lista'] = "ID do cliente inválido para edição.";
            header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=listar');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validarCpf($_POST['cpf'])) {
                $_SESSION['mensagem_erro_form'] = "CPF inválido.";
                $_SESSION['form_data'] = $_POST;
                header("Location: " . BASE_URL . "views/contatos/index.php?controller=cliente&action=editar&id_cliente=$idCliente");
                exit;
            }

            if (!validarTelefone($_POST['numero'])) {
                $_SESSION['mensagem_erro_form'] = "Número de telefone inválido.";
                $_SESSION['form_data'] = $_POST;
                header("Location: " . BASE_URL . "views/contatos/index.php?controller=cliente&action=editar&id_cliente=$idCliente");
                exit;
            }

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

            $idUsuarioAtribuido = $_POST['id_usuario'] ?? $clienteAtual['id_usuario'];
            
            // ALTERAÇÃO: Lógica para atualizar a imobiliária do cliente junto com o corretor
            $idImobiliariaFinal = $clienteAtual['id_imobiliaria'];
            $permissao = $_SESSION['usuario']['permissao'] ?? '';

            if ($permissao === 'SuperAdmin' && $idUsuarioAtribuido != $clienteAtual['id_usuario']) {
                $novoCorretor = $this->usuarioModel->buscarPorId($idUsuarioAtribuido);
                if ($novoCorretor) {
                    $idImobiliariaFinal = $novoCorretor['id_imobiliaria'];
                }
            }
            // FIM DA ALTERAÇÃO

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
                'id_usuario' => $idUsuarioAtribuido,
                'id_imobiliaria' => $idImobiliariaFinal // ALTERAÇÃO: Passa a imobiliária correta
            ];

            if ($this->clienteModel->atualizar($idCliente, $dadosAtualizar)) {
                $_SESSION['mensagem_sucesso'] = "Cliente atualizado com sucesso!";
                header("Location: " . BASE_URL . "views/contatos/index.php?controller=cliente&action=mostrar&id_cliente=$idCliente");
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao atualizar cliente.";
                $_SESSION['form_data'] = $_POST;
                header("Location: " . BASE_URL . "views/contatos/index.php?controller=cliente&action=editar&id_cliente=$idCliente");
            }

            exit;
        } else {
            $cliente = $this->clienteModel->buscarPorId($idCliente);
            if (!$cliente) {
                $_SESSION['mensagem_erro_lista'] = "Não foi possível carregar os dados do cliente para edição.";
                header('Location: ' . BASE_URL . 'views/contatos/index.php?controller=cliente&action=listar');
                exit;
            }
            
            $corretoresDisponiveis = [];
            $permissao = $_SESSION['usuario']['permissao'] ?? '';
            
            if (in_array($permissao, ['Admin', 'Coordenador', 'SuperAdmin'])) {
                $idImobiliariaParaListar = ($permissao === 'SuperAdmin') ? null : $cliente['id_imobiliaria'];
                $corretoresDisponiveis = $this->usuarioModel->listarPorImobiliaria($idImobiliariaParaListar);
            }

            require __DIR__ . '/../views/contatos/editar_cliente.php';
        }
    }

    // Exclui um cliente e sua foto, se existir
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
