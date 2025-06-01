<?php
session_start();
require_once '../config/config.php';
require_once '../models/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $usuario = new Usuario($connection);
    $dados   = $usuario->login($email, $senha);

    if ($dados) {
        // Se for SuperAdmin, não checa imobiliária—vai direto ao dashboard
        if ($dados['permissao'] === 'SuperAdmin') {
            $_SESSION['usuario'] = $dados;
            header('Location: ../views/dashboards/dashboard_superadmin.php');
            exit;
        }

        // Para todas as outras permissões, verifica vínculo com imobiliária
        if (
            !isset($dados['id_imobiliaria']) ||
            $dados['id_imobiliaria'] === '' ||
            $dados['id_imobiliaria'] === null ||
            $dados['id_imobiliaria'] == 0
        ) {
            // Usuário autenticado, mas sem imobiliária: redireciona para página de alerta
            header('Location: ../views/auth/sem_imobiliaria.php');
            exit;
        }

        // Se tiver imobiliária, salva na sessão e redireciona conforme permissão
        $_SESSION['usuario'] = $dados;

        switch ($dados['permissao']) {
            case 'Admin':
                header('Location: ../views/dashboards/dashboard_admin.php');
                break;
            case 'Coordenador':
                header('Location: ../views/dashboards/dashboard_coordenador.php');
                break;
            case 'Corretor':
                header('Location: ../views/dashboards/dashboard_corretor.php');
                break;
            default:
                echo "Permissão inválida. Contate o administrador.";
                exit;
        }

        exit;
    } else {
        // Credenciais incorretas: redireciona para página de “Login Inválido”
        header('Location: ../views/auth/login_invalido.php');
        exit;
    }
}
?>
