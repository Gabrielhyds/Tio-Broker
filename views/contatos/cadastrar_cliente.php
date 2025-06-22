<?php
@session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

$activeMenu = 'cliente_cadastrar';
$conteudo = 'cadastrar_cliente_content.php';
include '../layout/template_base.php';
