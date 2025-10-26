<?php
require_once '../../config/config.php';
require_once '../../models/Usuario.php';
// Outros models se necessário (ex: Cliente)

session_start();

// Proteção: Garante que o usuário está logado.
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}


$id_imobiliaria_logada = $_SESSION['usuario']['id_imobiliaria'] ?? 1;
$id_usuario_logado = $_SESSION['usuario']['id_usuario'] ?? 1;
$nome_usuario_logado = $_SESSION['usuario']['nome'] ?? 'Usuário';

$usuarioModel = new Usuario($connection);
$usuarios = $usuarioModel->listarTodos($id_imobiliaria_logada); // Lista usuários da mesma imobiliária

// Configuração da página
$activeMenu = 'leads'; // Menu ativo
$pageTitle = 'Cadastrar Novo Lead';
$conteudo = 'cadastrar_content.php'; // Arquivo de conteúdo
include '../layout/template_base.php'; // Inclui o layout base

?>
