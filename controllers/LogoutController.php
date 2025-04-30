<?php
session_start();

// Destroi todos os dados da sessão
session_unset();
session_destroy();

// Redireciona para a página de login
header('Location: ../views/auth/login.php');
exit;