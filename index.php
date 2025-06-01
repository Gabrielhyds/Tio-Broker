<?php
session_start();

// Se o usuário não estiver logado, envia para o login
if (!isset($_SESSION['usuario'])) {
    header('Location: views/auth/login.php');
    exit;
}

// Se estiver logado, verifica a permissão e redireciona
switch ($_SESSION['usuario']['permissao']) {
    case 'SuperAdmin':
        header('Location: views/dashboards/resumo.php');
        break;
    case 'Admin':
        header('Location: views/dashboards/dashboard_admin.php');
        break;
    case 'Coordenador':
        header('Location: views/dashboards/dashboard_coordenador.php');
        break;
    case 'Corretor':
        header('Location: views/dashboards/dashboard_corretor.php');
        break;
    default:
        echo "Permissão inválida. Contate o administrador.";
        exit;
}
exit;
