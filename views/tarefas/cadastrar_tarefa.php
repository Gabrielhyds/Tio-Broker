<?php

// O '@' suprime erros caso a sessão já tenha sido iniciada. Garante que podemos usar a superglobal $_SESSION.
@session_start();

// Verifica se não há um usuário na sessão, o que significa que o usuário não está logado.
if (!isset($_SESSION['usuario'])) {
    // Se não estiver logado, redireciona o navegador para a página de login.
    header('Location: ../auth/login.php');
    // Encerra a execução do script para garantir que o redirecionamento ocorra imediatamente.
    exit;
}

require_once '../../config/config.php';
$activeMenu = 'tarefas';
$conteudo = __DIR__ . '/cadastrar_tarefa_content.php'; // Caminho absoluto
include '../layout/template_base.php';
