<?php
session_start();
require_once '../config/config.php';
require_once '../models/Usuario.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'login') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $usuario = new Usuario($connection);
    $dados = $usuario->login($email, $senha);

    if ($dados) {
        $_SESSION['usuario'] = $dados;
    
        // Redireciona com base na permissão do usuário
        switch ($dados['permissao']) {
            case 'SuperAdmin':
                header('Location: ../views/dashboard_superadmin.php');
                break;
            case 'Admin':
                header('Location: ../views/dashboard_admin.php');
                break;
            case 'Coordenador':
                header('Location: ../views/dashboard_coordenador.php');
                break;
            case 'Corretor':
                header('Location: ../views/dashboard_corretor.php');
                break;
            default:
                echo "Permissão inválida. Contate o administrador.";
                exit;
        }
        exit;
    } else {
        echo "Login inválido.";
    }
}
?>
