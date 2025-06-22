<?php
@session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

if (!isset($cliente) || empty($cliente)) {
    $_SESSION['mensagem_erro'] = "Não foi possível carregar os dados do cliente para edição.";
    if (!headers_sent()) {
        header('Location: index.php?controller=cliente&action=listar');
        exit;
    } else {
        echo "<div class='alert alert-danger'>Erro crítico: Dados do cliente não encontrados. Contate o suporte.</div>";
        exit;
    }
}

$conteudo = 'editar_cliente_content.php';
include '../layout/template_base.php';
