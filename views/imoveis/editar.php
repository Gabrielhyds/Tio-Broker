<?php
// /views/imoveis/editar.php
// Este arquivo agora é apenas o "controlador da view"

require_once '../../config/config.php';
require_once '../../models/Imovel.php';
require_once '../../config/rotas.php';

session_start();

// Garante que o usuário está logado, se necessário
// if (!isset($_SESSION['usuario_id'])) {
//     header('Location: ../login.php');
//     exit;
// }

$imovelModel = new Imovel($connection);

// 1. Validação do ID
$id = $_GET['id'] ?? null;
if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
    $_SESSION['erro'] = "ID do imóvel inválido ou não informado.";
    header('Location: listar.php');
    exit;
}

// 2. Busca dos dados
try {
    $imovel = $imovelModel->buscarPorId($id);

    if (!$imovel) {
        $_SESSION['erro'] = "Imóvel não encontrado.";
        header('Location: listar.php');
        exit;
    }

    // Buscar os arquivos é importante para exibir no formulário
    $imagens = $imovelModel->buscarArquivos($id, 'imagem');
    $videos = $imovelModel->buscarArquivos($id, 'video');
    $documentos = $imovelModel->buscarArquivos($id, 'documento');
} catch (Exception $e) {
    $_SESSION['erro'] = "Erro ao carregar dados do imóvel: " . $e->getMessage();
    header('Location: listar.php');
    exit;
}


// 3. Preparação para o Template
$activeMenu = 'imoveis';
$tituloPagina = "Editar Imóvel: " . htmlspecialchars($imovel['titulo']);

// ✅ CORREÇÃO: Aponta para o arquivo que contém APENAS o HTML do formulário
$conteudo = 'editar_imovel.php';

// 4. Inclusão do Layout Principal
// O template_base.php irá incluir o arquivo '_formulario_edicao.php' no local correto.
include '../layout/template_base.php';
