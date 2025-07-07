<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Imovel.php';

// Inicia a sessão para usar $_SESSION e obter dados do usuário.
session_start();

// Proteção: Garante que o usuário está logado.
if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/auth/login.php');
    exit;
}

// Cria uma instância do Model.
$imovelModel = new Imovel($connection);

// Verifica a ação enviada via GET ou POST.
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// --- Bloco de Ações ---
switch ($action) {
    case 'cadastrar':
        cadastrarImovel($imovelModel);
        break;

    case 'editar':
        editarImovel($imovelModel);
        break;

    case 'excluir':
        $id = $_GET['id'] ?? null;
        if ($id && $imovelModel->excluir($id)) {
            $_SESSION['sucesso'] = "Imóvel excluído com sucesso.";
        } else {
            $_SESSION['erro'] = "Erro ao excluir imóvel.";
        }
        header('Location: ../views/imoveis/listar.php');
        exit;

    case 'excluir_arquivo':
        $tipo = $_POST['tipo'] ?? '';
        $idArquivo = $_POST['id_arquivo'] ?? null;
        $idImovel = $_POST['id_imovel'] ?? null;

        if ($tipo && $idArquivo && $imovelModel->excluirArquivo($tipo, $idArquivo)) {
            $_SESSION['sucesso'] = ucfirst($tipo) . " excluído com sucesso.";
        } else {
            $_SESSION['erro'] = "Erro ao excluir o arquivo.";
        }

        header("Location: ../views/imoveis/editar.php?id=" . $idImovel);
        exit;

    default:
        // Se nenhuma ação específica for chamada, redireciona para a listagem.
        header('Location: ../views/imoveis/listar.php');
        exit;
}

// --- Funções Auxiliares ---

/**
 * Orquestra o cadastro de um novo imóvel.
 */
function cadastrarImovel($model)
{
    $dados = coletarDados();

    // Salva os arquivos enviados.
    $imagens = salvarUploads('imagens', 'imagens_imoveis');
    $videos = salvarUploads('videos', 'videos_imoveis');
    $documentos = salvarUploads('documentos', 'documentos_imoveis');

    // Chama o método de cadastro no Model.
    if ($model->cadastrar($dados, $imagens, $videos, $documentos)) {
        $_SESSION['sucesso'] = "Imóvel cadastrado com sucesso.";
    } else {
        $_SESSION['erro'] = "Erro ao cadastrar imóvel.";
    }

    header('Location: ../views/imoveis/listar.php');
    exit;
}

/**
 * Orquestra a edição de um imóvel existente.
 */
function editarImovel($model)
{
    $id = $_POST['id_imovel'] ?? null;
    if (!$id) {
        $_SESSION['erro'] = "ID do imóvel não informado.";
        header('Location: ../views/imoveis/listar.php');
        exit;
    }

    $dados = coletarDados();
    $dados['id_imovel'] = $id;

    $imagens = salvarUploads('imagens', 'imagens_imoveis');
    $videos = salvarUploads('videos', 'videos_imoveis');
    $documentos = salvarUploads('documentos', 'documentos_imoveis');

    if ($model->editar($dados, $imagens, $videos, $documentos)) {
        $_SESSION['sucesso'] = "Imóvel atualizado com sucesso.";
    } else {
        $_SESSION['erro'] = "Erro ao atualizar imóvel.";
    }

    // Redireciona de volta para a página de edição.
    header("Location: ../views/imoveis/editar.php?id=" . $id);
    exit;
}

/**
 * Coleta os dados do formulário e adiciona o id_imobiliaria da sessão.
 */
function coletarDados()
{
    // Adiciona o id_imobiliaria do usuário logado aos dados do imóvel.
    $id_imobiliaria = $_SESSION['usuario']['id_imobiliaria'] ?? null;

    return [
        'id_imobiliaria' => $id_imobiliaria,
        'titulo' => $_POST['titulo'] ?? '',
        'descricao' => $_POST['descricao'] ?? '',
        'tipo' => $_POST['tipo'] ?? '',
        'status' => $_POST['status'] ?? '',
        // ✅ CORREÇÃO: Converte o valor para float, preservando o decimal.
        // O JavaScript já envia o valor no formato "1234.56".
        'preco' => (float) ($_POST['preco'] ?? 0),
        'endereco' => $_POST['endereco'] ?? '',
        'latitude' => !empty($_POST['latitude']) ? $_POST['latitude'] : null,
        'longitude' => !empty($_POST['longitude']) ? $_POST['longitude'] : null,
    ];
}

/**
 * Processa o upload de múltiplos arquivos.
 */
function salvarUploads($campo, $subpasta)
{
    $arquivosSalvos = [];
    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'mp4', 'doc', 'docx', 'xls', 'xlsx'];

    // Define o caminho absoluto para salvar os arquivos.
    // Crie uma constante UPLOADS_DIR no seu config.php, ex: define('UPLOADS_DIR', __DIR__ . '/../uploads/');
    if (!defined('UPLOADS_DIR')) {
        // Fallback caso a constante não esteja definida.
        define('UPLOADS_DIR', __DIR__ . '/../uploads/');
    }

    $destinoRelativo = 'uploads/' . trim($subpasta, '/') . '/';
    $destinoAbsoluto = UPLOADS_DIR . trim($subpasta, '/') . '/';

    if (!empty($_FILES[$campo]['name'][0])) {
        if (!is_dir($destinoAbsoluto)) {
            mkdir($destinoAbsoluto, 0777, true);
        }

        foreach ($_FILES[$campo]['tmp_name'] as $index => $tmp) {
            if (empty($tmp)) continue; // Pula arquivos vazios

            $extensao = strtolower(pathinfo($_FILES[$campo]['name'][$index], PATHINFO_EXTENSION));
            if (!in_array($extensao, $permitidas)) continue; // Pula arquivos não permitidos

            $nome = uniqid() . '_' . basename($_FILES[$campo]['name'][$index]);
            $caminhoCompleto = $destinoAbsoluto . $nome;
            $caminhoRelativo = $destinoRelativo . $nome;

            if (move_uploaded_file($tmp, $caminhoCompleto)) {
                $arquivosSalvos[] = $caminhoRelativo;
            }
        }
    }

    return $arquivosSalvos;
}
