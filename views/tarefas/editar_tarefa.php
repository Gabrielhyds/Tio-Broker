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
require_once '../../controllers/TarefaController.php';

$controller = new TarefaController($connection);
$tarefa = $controller->buscarInterno($_GET['id_tarefa'] ?? 0);

$activeMenu = 'tarefas';
$conteudo = 'editar_tarefa_content.php';
include '../layout/template_base.php';
