<?php
@session_start();

// Se o usuário não estiver logado, envia para o login
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

$activeMenu = 'cliente_listar';
$conteudo = 'listar_cliente_content.php';
include '../layout/template_base.php';
