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

// Define uma variável para identificar o menu ativo. Isso será usado na 'sidebar' para destacar o link correto.
$activeMenu = 'cliente_cadastrar';
// Define o nome do arquivo que contém o conteúdo principal desta página (o formulário de cadastro).
$conteudo = 'cadastrar_cliente_content.php';
// Inclui o arquivo de layout base, que montará a estrutura da página (cabeçalho, rodapé, etc.) e incluirá o arquivo de conteúdo definido acima.
include '../layout/template_base.php';
