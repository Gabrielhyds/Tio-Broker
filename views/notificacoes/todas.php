<?php
// views/notificacoes/todas.php
if (session_status() === PHP_SESSION_NONE) session_start();

// Proteção
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Define qual menu deve ficar "ativo" (nenhum, neste caso)
$activeMenu = ''; 

// Define o arquivo de conteúdo que o template_base.php vai carregar
$conteudo = __DIR__ . '/todas_content.php';

// Carrega o template principal
include __DIR__ . '/../layout/template_base.php';
?>
