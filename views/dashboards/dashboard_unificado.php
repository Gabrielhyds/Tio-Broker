<?php
// Garante que uma sessão PHP seja iniciada, se ainda não houver uma ativa.
if (session_status() === PHP_SESSION_NONE) session_start();
// Obtém os dados do usuário da sessão, usando o operador de coalescência nula para definir um valor padrão se não existir.
$usuario = $_SESSION['usuario'] ?? null;

// Se não houver usuário na sessão, redireciona para a página de login.
if (!$usuario) {
    header('Location: ../auth/login.php');
    exit;
}

// Define qual item do menu de navegação deve ser marcado como "ativo".
$activeMenu = 'dashboard';

// --- ESTA É A MUDANÇA ---
// Antes: apontava para o arquivo antigo que misturava PHP e HTML
// $conteudo = __DIR__ . '/dashboard_unificado_content.php';

// Agora: aponta para o seu novo arquivo de frontend Vue
$conteudo = __DIR__ . '/dashboard_com_api.php'; // <--- MUDOU AQUI

// Inclui o template base da página (que carrega o Vue.js, CSS, etc.)
include __DIR__ . '/../layout/template_base.php';