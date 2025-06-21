<?php
// Inicia a sessão e obtém dados do usuário
if (session_status() === PHP_SESSION_NONE) session_start();
$usuario = $_SESSION['usuario'] ?? null;
$permissao = $usuario['permissao'] ?? 'Desconhecido';
$nomeUsuario = $usuario['nome'] ?? 'Usuário';

// Define qual conteúdo será incluído dentro do template base
$conteudo = __DIR__ . '/dashboard_unificado_content.php';
include __DIR__ . '/../layout/template_base.php';
