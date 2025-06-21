<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../../config/config.php';
require_once '../../models/Imobiliaria.php';

$imobiliaria = new Imobiliaria($connection);

// CORREÇÃO: filtro por permissão
$usuarioLogado = $_SESSION['usuario'];
$permissao = $usuarioLogado['permissao'] ?? '';
$id_imobiliaria = $usuarioLogado['id_imobiliaria'] ?? null;

if ($permissao === 'SuperAdmin') {
    $listaImobiliarias = $imobiliaria->listarTodas(); // todas
} else {
    $imob = $imobiliaria->buscarPorId($id_imobiliaria); // só a dele
    $listaImobiliarias = $imob ? [$imob] : [];
}

$activeMenu = 'usuario_listar';
$conteudo = 'cadastrar_conteudo.php';
include '../layout/template_base.php';
