<?php
@session_start();

// Se o usuário não estiver logado, envia para o login
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

$activeMenu = 'imobiliaria_cadastrar';
$conteudo = 'cadastrar_imobiliaria_content.php';
include '../layout/template_base.php';
