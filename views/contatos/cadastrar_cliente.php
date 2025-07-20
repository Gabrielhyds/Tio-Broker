<?php
// Garante que a sessão está iniciada.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado. Se não, redireciona para o login.
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Define qual menu da sidebar deve ficar ativo.
$activeMenu = 'contatos';

// CORREÇÃO: Aponta para o arquivo de conteúdo de CADASTRO.
$conteudo = 'cadastrar_cliente_content.php'; 

// Inclui o template base que montará a página completa.
include __DIR__ . '/../layout/template_base.php';