<?php
@session_start();

// Se o usuário não estiver logado, envia para o login
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Define o menu ativo para destacar na sidebar
$activeMenu = 'agenda'; 

// Define o arquivo de conteúdo que será carregado
$conteudo = 'agenda_content.php';

// Inclui o template base que monta a página
include '../layout/template_base.php';
