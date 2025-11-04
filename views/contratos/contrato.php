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

// Redefine a variável de menu ativo (embora já definida, é uma boa prática garantir).
$activeMenu = 'contratos';
// Define o nome do arquivo que contém o HTML da lista de imobiliárias.
$conteudo = __DIR__ . '/contrato_content.php';
// Inclui o template base da página, que usará a variável $conteudo para carregar o HTML correto.
include '../layout/template_base.php';


?>