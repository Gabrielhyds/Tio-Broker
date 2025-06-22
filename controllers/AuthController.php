<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../config/rotas.php'; // BASE_URL

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $usuario = new Usuario($connection);
    $dados   = $usuario->login($email, $senha);

    if ($dados) {
        // Verifica vínculo com imobiliária (exceto para SuperAdmin)
        if (
            $dados['permissao'] !== 'SuperAdmin' &&
            (
                !isset($dados['id_imobiliaria']) ||
                $dados['id_imobiliaria'] === '' ||
                $dados['id_imobiliaria'] === null ||
                $dados['id_imobiliaria'] == 0
            )
        ) {
            header('Location: ../views/auth/sem_imobiliaria.php');
            exit;
        }

        // Login válido: salva usuário na sessão e redireciona para dashboard unificado
        $_SESSION['usuario'] = $dados;
        header('Location: ' . BASE_URL . 'views/dashboards/dashboard_unificado.php');
        exit;
    } else {
        // Falha de login
        header('Location: ../views/auth/login_invalido.php');
        exit;
    }
}
