<?php
// Inicia ou resume a sessão PHP, permitindo o acesso aos dados da sessão, como as informações do usuário logado.
session_start();
// Verifica se a variável de sessão 'usuario' NÃO está definida. Se não estiver, significa que o usuário não está logado.
if (!isset($_SESSION['usuario'])) {
    // Redireciona o navegador do usuário para a página de login.
    header('Location: views/auth/login.php');
    // Encerra a execução do script para garantir que nada mais seja processado após o redirecionamento.
    exit;
}

// Armazena todos os dados do usuário da sessão em uma variável local para fácil acesso.
$usuario = $_SESSION['usuario'];
// Armazena especificamente o nível de permissão do usuário.
$permissao = $_SESSION['usuario']['permissao'];

// Define o nome do arquivo de conteúdo que será carregado dinamicamente dentro do template principal.
$conteudo = '../dashboards/dashboard_unificado.php';
// Inclui o arquivo de layout base (template), que provavelmente contém o cabeçalho, menu lateral e rodapé.
// Este template usará a variável $conteudo para saber qual página carregar na área principal.
include 'views/layout/template_base.php';
// Encerra o script para garantir que o fluxo de execução termine aqui.
exit;
