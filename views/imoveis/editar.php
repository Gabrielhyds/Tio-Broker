<?php
require_once '../../config/config.php';
require_once '../../models/Imovel.php';
require_once '../../config/rotas.php';

session_start();

$imovelModel = new Imovel($connection);

// Verifica se o ID foi passado
$id = $_GET['id'] ?? null;
if (!$id) {
    $_SESSION['erro'] = "ID do imóvel não informado.";
    header('Location: listar.php');
    exit;
}

// Busca o imóvel
$imovel = $imovelModel->buscarPorId($id);
if (!$imovel) {
    $_SESSION['erro'] = "Imóvel não encontrado.";
    header('Location: listar.php');
    exit;
}

// 🔥 IMPORTANTE: Buscar os arquivos relacionados
$imagens = $imovelModel->buscarArquivos($id, 'imagem');
$videos = $imovelModel->buscarArquivos($id, 'video');
$documentos = $imovelModel->buscarArquivos($id, 'documento');

// Inclui o template visual
$activeMenu = 'imoveis';
$tituloPagina = "Editar Imóvel";
$conteudo = 'editar_imovel.php';
include '../layout/template_base.php';
