<?php
// Garante que uma sessão PHP seja iniciada, se ainda não houver uma ativa.
if (session_status() === PHP_SESSION_NONE) session_start();
// Obtém os dados do usuário da sessão, usando o operador de coalescência nula para definir um valor padrão se não existir.
$usuario = $_SESSION['usuario'] ?? null;
// Obtém a permissão do usuário, com 'Desconhecido' como padrão.
$permissao = $usuario['permissao'] ?? 'Desconhecido';
// Obtém o nome do usuário, com 'Usuário' como padrão.
$nomeUsuario = $usuario['nome'] ?? 'Usuário';

// Define o caminho para o arquivo que contém o conteúdo visual do dashboard.
$conteudo = __DIR__ . '/dashboard_unificado_content.php';
// Inclui o template base da página, que por sua vez carregará o arquivo de conteúdo definido acima.
include __DIR__ . '/../layout/template_base.php';
