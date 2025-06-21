<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: views/auth/login.php');
    exit;
}

$usuario = $_SESSION['usuario'];
$permissao = $_SESSION['usuario']['permissao'];

$conteudo = '../dashboards/dashboard_unificado.php';
include 'views/layout/template_base.php'; // ou o caminho correto
exit;
