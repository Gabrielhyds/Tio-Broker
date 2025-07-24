<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/rotas.php';
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

    $model->connection->begin_transaction();

    try {
        $imagens = salvarUploads('imagens', 'imagens_imoveis');
        $videos = salvarUploads('videos', 'videos_imoveis');
        $documentos = salvarUploads('documentos', 'documentos_imoveis');

        $model->cadastrar($dados, $imagens, $videos, $documentos);

        $model->connection->commit();
        $_SESSION['sucesso'] = "Imóvel cadastrado com sucesso.";
    } catch (Exception $e) {
        $model->connection->rollback();
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

    $model->connection->begin_transaction();

    try {
        $imagens = salvarUploads('imagens', 'imagens_imoveis');
        $videos = salvarUploads('videos', 'videos_imoveis');
        $documentos = salvarUploads('documentos', 'documentos_imoveis');

        $model->editar($dados, $imagens, $videos, $documentos);

        $model->connection->commit();
        $_SESSION['sucesso'] = "Imóvel atualizado com sucesso.";
    } catch (Exception $e) {
        $model->connection->rollback();
        $_SESSION['erro'] = "Erro ao atualizar imóvel: " . $e->getMessage();
    }

    header("Location: ../views/imoveis/editar.php?id=" . $id);
    exit;
}

/**
 * Coleta os dados do formulário com a nova estrutura de endereço.
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
        'endereco' => $_POST['endereco'] ?? '', // Logradouro
        // ✅ CAMPOS REFATORADOS: Latitude e Longitude foram substituídos.
        'cep' => $_POST['cep'] ?? '',
        'bairro' => $_POST['bairro'] ?? '',
        'cidade' => $_POST['cidade'] ?? '',
        'estado' => $_POST['estado'] ?? '',
        'numero' => $_POST['numero'] ?? '',
        'complemento' => $_POST['complemento'] ?? '',
    ];
}

/**
 * Processa o upload de múltiplos arquivos.
 */
function salvarUploads($campo, $subpasta)
{
    $arquivosSalvos = [];
    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'mp4', 'doc', 'docx', 'xls', 'xlsx', 'webp'];

    if (!defined('UPLOADS_DIR')) {
        throw new Exception("UPLOADS_DIR não está definido. Verifique se config.php foi incluído.");
    }
    $destinoRelativo = 'uploads/' . trim($subpasta, '/') . '/';
    $destinoAbsoluto = UPLOADS_DIR . trim($subpasta, '/') . '/';

    if (!empty($_FILES[$campo]['name'][0])) {
        
        if (!is_dir($destinoAbsoluto)) {
            if (!is_writable(UPLOADS_DIR)) {
                 throw new Exception("Erro de permissão: O diretório de uploads principal ('" . UPLOADS_DIR . "') não tem permissão de escrita pelo servidor.");
            }
            if (!mkdir($destinoAbsoluto, 0755, true)) {
                throw new Exception("Falha ao criar a pasta de uploads ('" . $destinoAbsoluto . "'). Verifique as permissões do servidor.");
            }
        }

        foreach ($_FILES[$campo]['tmp_name'] as $index => $tmp) {
            if (empty($tmp) || $_FILES[$campo]['error'][$index] !== UPLOAD_ERR_OK) {
                continue;
            }

            $extensao = strtolower(pathinfo($_FILES[$campo]['name'][$index], PATHINFO_EXTENSION));
            if (!in_array($extensao, $permitidas)) continue;

            $nomeArquivoOriginal = basename($_FILES[$campo]['name'][$index]);
            $nomeSeguro = preg_replace("/[^a-zA-Z0-9-_\.]/", "", pathinfo($nomeArquivoOriginal, PATHINFO_FILENAME));
            $nomeFinal = $nomeSeguro . '_' . uniqid() . '.' . $extensao;
            $caminhoCompleto = $destinoAbsoluto . $nomeFinal;
            $caminhoRelativo = $destinoRelativo . $nomeFinal;

            if (move_uploaded_file($tmp, $caminhoCompleto)) {
                $arquivosSalvos[] = $caminhoRelativo;
            } else {
                throw new Exception("Falha ao mover o arquivo enviado: " . $nomeArquivoOriginal);
            }
        }
    }

    return $arquivosSalvos;
}
