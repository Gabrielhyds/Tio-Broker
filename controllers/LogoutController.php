<?php
// Inicia a sessão (necessário para manipular dados da sessão atual)
session_start();

// Remove todas as variáveis da sessão
session_unset();

// Destroi completamente a sessão do usuário
session_destroy();

// Redireciona o usuário de volta para a página de login
header('Location: ../views/auth/login.php');

exit; // Garante que o script será encerrado após o redirecionamento


?>
