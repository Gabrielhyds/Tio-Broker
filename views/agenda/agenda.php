<?php
// Inicia ou resume uma sessão PHP existente. O '@' suprime erros caso a sessão já tenha sido iniciada.
@session_start();

// Verifica se a variável de sessão 'usuario' NÃO está definida. Isso é usado para checar se o usuário está logado.
if (!isset($_SESSION['usuario'])) {
    // Se o usuário não estiver logado, redireciona o navegador para a página de login.
    header('Location: ../auth/login.php');
    // Encerra a execução do script para garantir que o redirecionamento ocorra imediatamente.
    exit;
}

// Define uma variável para identificar o menu ativo. Isso é útil para destacar o item "Agenda" na barra lateral de navegação.
$activeMenu = 'agenda';

// Define o nome do arquivo de conteúdo específico que será carregado dentro do layout principal.
$conteudo = 'agenda_content.php';

// Inclui o arquivo de template base, que provavelmente contém o cabeçalho, a barra lateral e o rodapé da página.
// Este template usará as variáveis $activeMenu e $conteudo para montar a página final.
include '../layout/template_base.php';
