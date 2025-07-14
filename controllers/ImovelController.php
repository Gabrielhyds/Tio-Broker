<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Imovel.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header('Location: ../views/auth/login.php');
    exit;
}

$imovelModel = new Imovel($connection);
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
        $idImobiliariaQuery = isset($_GET['id_imobiliaria']) ? '&id_imobiliaria=' . $_GET['id_imobiliaria'] : '';

        if ($id && $imovelModel->excluir($id)) {
            $_SESSION['sucesso'] = "Imóvel excluído com sucesso.";
        } else {
            $_SESSION['erro'] = "Erro ao excluir imóvel.";
        }
        header('Location: ../views/imoveis/listar.php?' . ltrim($idImobiliariaQuery, '&'));
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
        header('Location: ../views/imoveis/listar.php');
        exit;
}

/**
 * Orquestra o cadastro de um novo imóvel usando transações.
 */
function cadastrarImovel($model)
{
    $dados = coletarDados();
    if (empty($dados['id_imobiliaria'])) {
        $_SESSION['erro'] = "A imobiliária para este imóvel não foi definida. Selecione uma antes de cadastrar.";
        header('Location: ../views/imoveis/listar.php');
        exit;
    }

    // Inicia a transação
    $model->connection->begin_transaction();

    try {
        $imagens = salvarUploads('imagens', 'imagens_imoveis');
        $videos = salvarUploads('videos', 'videos_imoveis');
        $documentos = salvarUploads('documentos', 'documentos_imoveis');

        // **AÇÃO NECESSÁRIA**: Altere seu método no Model para lançar uma exceção em caso de erro.
        // Ex: if (!$stmt->execute()) { throw new Exception($stmt->error); }
        $model->cadastrar($dados, $imagens, $videos, $documentos);

        // Se tudo deu certo, confirma a transação
        $model->connection->commit();
        $_SESSION['sucesso'] = "Imóvel cadastrado com sucesso.";
    } catch (Exception $e) {
        // Se algo deu errado, desfaz a transação
        $model->connection->rollback();
        // Salva a mensagem de erro real para depuração
        $_SESSION['erro'] = "Erro ao cadastrar imóvel: " . $e->getMessage();
    }

    header('Location: ../views/imoveis/listar.php?id_imobiliaria=' . $dados['id_imobiliaria']);
    exit;
}

/**
 * Orquestra a edição de um imóvel existente usando transações.
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

    // Inicia a transação
    $model->connection->begin_transaction();

    try {
        $imagens = salvarUploads('imagens', 'imagens_imoveis');
        $videos = salvarUploads('videos', 'videos_imoveis');
        $documentos = salvarUploads('documentos', 'documentos_imoveis');

        // **AÇÃO NECESSÁRIA**: Altere seu método no Model para lançar uma exceção em caso de erro.
        $model->editar($dados, $imagens, $videos, $documentos);

        // Se tudo deu certo, confirma a transação
        $model->connection->commit();
        $_SESSION['sucesso'] = "Imóvel atualizado com sucesso.";
    } catch (Exception $e) {
        // Se algo deu errado, desfaz a transação
        $model->connection->rollback();
        $_SESSION['erro'] = "Erro ao atualizar imóvel: " . $e->getMessage();
    }

    header("Location: ../views/imoveis/editar.php?id=" . $id);
    exit;
}

/**
 * Coleta os dados do formulário e define o id_imobiliaria corretamente.
 */
function coletarDados()
{
    $id_imobiliaria = $_POST['id_imobiliaria'] ?? $_SESSION['usuario']['id_imobiliaria'] ?? null;

    return [
        'id_imobiliaria' => $id_imobiliaria,
        'titulo' => $_POST['titulo'] ?? '',
        'descricao' => $_POST['descricao'] ?? '',
        'tipo' => $_POST['tipo'] ?? '',
        'status' => $_POST['status'] ?? '',
        'preco' => (float) ($_POST['preco'] ?? 0),
        'endereco' => $_POST['endereco'] ?? '',
        'latitude' => !empty($_POST['latitude']) ? $_POST['latitude'] : null,
        'longitude' => !empty($_POST['longitude']) ? $_POST['longitude'] : null,
    ];
}

/**
 * Processa o upload de múltiplos arquivos.
 * Lança uma exceção se o upload falhar.
 */
function salvarUploads($campo, $subpasta)
{
    $arquivosSalvos = [];
    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'mp4', 'doc', 'docx', 'xls', 'xlsx'];

    if (!defined('UPLOADS_DIR')) {
        define('UPLOADS_DIR', __DIR__ . '/../uploads/');
    }

    $destinoRelativo = 'uploads/' . trim($subpasta, '/') . '/';
    $destinoAbsoluto = UPLOADS_DIR . trim($subpasta, '/') . '/';

    if (!empty($_FILES[$campo]['name'][0])) {
        if (!is_dir($destinoAbsoluto)) {
            if (!mkdir($destinoAbsoluto, 0755, true)) {
                throw new Exception("Falha ao criar a pasta de uploads.");
            }
        }

        foreach ($_FILES[$campo]['tmp_name'] as $index => $tmp) {
            if (empty($tmp) || $_FILES[$campo]['error'][$index] !== UPLOAD_ERR_OK) {
                continue;
            }

            $extensao = strtolower(pathinfo($_FILES[$campo]['name'][$index], PATHINFO_EXTENSION));
            if (!in_array($extensao, $permitidas)) continue;

            $nomeArquivoOriginal = basename($_FILES[$campo]['name'][$index]);
            $nomeSeguro = preg_replace("/[^a-zA-Z0-9-_\.]/", "", $nomeArquivoOriginal);
            $nomeFinal = uniqid() . '_' . $nomeSeguro;
            $caminhoCompleto = $destinoAbsoluto . $nomeFinal;
            $caminhoRelativo = $destinoRelativo . $nomeFinal;

            if (move_uploaded_file($tmp, $caminhoCompleto)) {
                $arquivosSalvos[] = $caminhoRelativo;
            } else {
                // Se o upload falhar, lança uma exceção para acionar o rollback.
                throw new Exception("Falha ao mover o arquivo enviado: " . $nomeArquivoOriginal);
            }
        }
    }

    return $arquivosSalvos;
}
