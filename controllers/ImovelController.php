<?php
require_once '../config/config.php';
require_once '../models/Imovel.php';
require_once '../config/rotas.php';
session_start();

$imovelModel = new Imovel($connection);

// Verifica a ação enviada via GET ou POST
$action = $_POST['action'] ?? $_GET['action'] ?? '';

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
        break;

    case 'excluir_arquivo':
        // Incorreto:
        // $tipo = $_POST['tipo_arquivo'] ?? ''; 

        // ✅ Correto:
        $tipo = $_POST['tipo'] ?? ''; // O formulário envia 'tipo'

        $idArquivo = $_POST['id_arquivo'] ?? null;
        $idImovel = $_POST['id_imovel'] ?? null;

        if ($tipo && $idArquivo) {
            $imovelModel->excluirArquivo($tipo, $idArquivo);
            $_SESSION['sucesso'] = ucfirst($tipo) . " excluído com sucesso.";
        } else {
            $_SESSION['erro'] = "Erro ao excluir o arquivo.";
        }

        header("Location: ../views/imoveis/editar.php?id=" . $idImovel);
        exit;

    default:
        header('Location: ../views/imoveis/listar.php');
        break;
}

// --- Funções ---

function cadastrarImovel($model)
{
    $dados = coletarDados();

    $imagens = salvarUploads('imagens', 'imagens_imoveis');
    $videos = salvarUploads('videos', 'videos_imoveis');
    $documentos = salvarUploads('documentos', 'documentos_imoveis');

    if ($model->cadastrar($dados, $imagens, $videos, $documentos)) {
        $_SESSION['sucesso'] = "Imóvel cadastrado com sucesso.";
    } else {
        $_SESSION['erro'] = "Erro ao cadastrar imóvel.";
    }

    header('Location: ../views/imoveis/listar.php');
}

function editarImovel($model)
{
    $id = $_POST['id_imovel'] ?? null;
    if (!$id) {
        $_SESSION['erro'] = "ID do imóvel não informado.";
        header('Location: ../views/imoveis/listar.php');
        return;
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

    // Redireciona de volta para a edição do mesmo imóvel
    header("Location: ../views/imoveis/editar.php?id=" . $id);
}

function coletarDados()
{
    return [
        'titulo' => $_POST['titulo'] ?? '',
        'descricao' => $_POST['descricao'] ?? '',
        'tipo' => $_POST['tipo'] ?? '',
        'status' => $_POST['status'] ?? '',
        'preco' => $_POST['preco'] ?? 0,
        'endereco' => $_POST['endereco'] ?? '',
        'latitude' => $_POST['latitude'] ?? null,
        'longitude' => $_POST['longitude'] ?? null,
    ];
}

function salvarUploads($campo, $subpasta)
{
    $arquivosSalvos = [];

    // Incompleto:
    // $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'mp4'];

    // ✅ Correto (adicionando as extensões de documento):
    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'mp4', 'doc', 'docx'];

    $destinoRelativo = 'uploads/' . trim($subpasta, '/') . '/';
    $destinoAbsoluto = UPLOADS_DIR . trim($subpasta, '/') . '/';

    if (!empty($_FILES[$campo]['name'][0])) {
        if (!is_dir($destinoAbsoluto)) mkdir($destinoAbsoluto, 0777, true);

        foreach ($_FILES[$campo]['tmp_name'] as $index => $tmp) {
            $extensao = strtolower(pathinfo($_FILES[$campo]['name'][$index], PATHINFO_EXTENSION));
            if (!in_array($extensao, $permitidas)) continue;

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
