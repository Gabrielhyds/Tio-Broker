<?php
// Inicia ou resume a sessão atual. É necessário para poder acessar e manipular os dados da sessão.
session_start();

// Remove todas as variáveis de sessão que foram definidas (ex: $_SESSION['usuario'], $_SESSION['erro'], etc.).
session_unset();

// Destrói completamente a sessão atual no servidor. Isso invalida o ID de sessão do usuário.
session_destroy();

// Após destruir a sessão, redireciona o navegador do usuário para a página de login.
header('Location: ../views/auth/login.php');

// Garante que o script pare de ser executado imediatamente após o comando de redirecionamento.
exit;
