<?php
session_start();
require_once '../../config/config.php';
require_once '../../models/Imovel.php';

$activeMenu = 'imovel_listar';
$titulo_pagina = 'Detalhes do Imóvel';
$conteudo = 'mostrar_imovel.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    $_SESSION['erro'] = "Imóvel não encontrado.";
    header('Location: listar.php');
    exit;
}

$model = new Imovel($connection);
$imovel = $model->buscarPorId($id);
$imagens = $model->buscarArquivos($id, 'imagem');
$videos = $model->buscarArquivos($id, 'video');
$documentos = $model->buscarArquivos($id, 'documento');

if (!$imovel) {
    $_SESSION['erro'] = "Imóvel não encontrado.";
    header('Location: listar.php');
    exit;
}

include '../layout/template_base.php';
