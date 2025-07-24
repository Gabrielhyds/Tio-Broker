<?php
// O '@' suprime erros caso a sessão já tenha sido iniciada.
@session_start();

// Se o usuário não estiver logado na sessão, redireciona para a página de login.
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Verifica se a variável $cliente não foi definida ou está vazia.
// Esta variável deve ser preenchida pelo script que chama este arquivo.
if (!isset($cliente) || empty($cliente)) {
    // Se não houver dados do cliente, define uma mensagem de erro na sessão.
    $_SESSION['erro'] = "Não foi possível carregar os dados do cliente para edição.";
    // Verifica se os cabeçalhos HTTP ainda não foram enviados para o navegador.
    if (!headers_sent()) {
        // Se não foram enviados, redireciona para a página de listagem de clientes.
        header('Location: index.php?controller=cliente&action=listar');
        exit;
    } else {
        // Se os cabeçalhos já foram enviados, exibe uma mensagem de erro diretamente na página.
        echo "<div class='alert alert-danger'>Erro crítico: Dados do cliente não encontrados. Contate o suporte.</div>";
        exit;
    }
}

// Define o nome do arquivo que contém o conteúdo principal desta página (o formulário de edição).
$conteudo = 'editar_cliente_content.php';
// Inclui o arquivo de layout base, que montará a estrutura da página (cabeçalho, rodapé, etc.) e incluirá o arquivo de conteúdo definido acima.
include '../layout/template_base.php';
