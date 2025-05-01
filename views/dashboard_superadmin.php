<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['permissao'] !== 'SuperAdmin') {
    header('Location: ../auth/login.php');
    exit;
}

$nomeUsuario = $_SESSION['usuario']['nome'] ?? 'SuperAdmin';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel SuperAdmin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', sans-serif;
            padding-top: 70px; /* espaço para a navbar fixa */
        }

        .dashboard-container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .card-custom {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }

        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #0d6efd;
        }

        .card-text {
            color: #6c757d;
        }

        .icon-box {
            font-size: 2rem;
            color: #0d6efd;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <!-- Navbar Fixa -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold">Sistema Imobiliário</span>
            <div class="d-flex align-items-center">
                <span class="text-white me-3"><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($nomeUsuario) ?></span>
                <a href="../controllers/LogoutController.php" class="btn btn-outline-light btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <!-- Conteúdo da Dashboard -->
    <div class="container dashboard-container py-5">
        <h1 class="mb-4 text-center">Painel de Controle - SuperAdmin</h1>

        <div class="row g-4">
            <!-- Cadastrar Imobiliária -->
            <div class="col-md-4">
                <a href="imobiliarias/cadastrar.php" class="text-decoration-none">
                    <div class="card card-custom text-center p-4">
                        <div class="icon-box"><i class="bi bi-building-add"></i></div>
                        <h5 class="card-title">Cadastrar Imobiliária</h5>
                        <p class="card-text">Adicione uma nova empresa à plataforma.</p>
                    </div>
                </a>
            </div>

            <!-- Listar Imobiliárias -->
            <div class="col-md-4">
                <a href="imobiliarias/listar.php" class="text-decoration-none">
                    <div class="card card-custom text-center p-4">
                        <div class="icon-box"><i class="bi bi-buildings"></i></div>
                        <h5 class="card-title">Ver Imobiliárias</h5>
                        <p class="card-text">Visualize e gerencie imobiliárias cadastradas.</p>
                    </div>
                </a>
            </div>

            <!-- Gerenciar Usuários -->
            <div class="col-md-4">
                <a href="usuarios/listar.php" class="text-decoration-none">
                    <div class="card card-custom text-center p-4">
                        <div class="icon-box"><i class="bi bi-people-fill"></i></div>
                        <h5 class="card-title">Gerenciar Usuários</h5>
                        <p class="card-text">Associe e visualize usuários por imobiliária.</p>
                    </div>
                </a>
            </div>
            
            <!-- Cadastrar Usuário -->
            <div class="col-md-4">
                <a href="usuarios/cadastrar.php" class="text-decoration-none">
                    <div class="card card-custom text-center p-4">
                        <div class="icon-box"><i class="bi bi-person-plus-fill"></i></div>
                        <h5 class="card-title">Cadastrar Usuário</h5>
                        <p class="card-text">Crie usuários e vincule a uma imobiliária.</p>
                    </div>
                </a>
            </div>

            <!-- Chat interno -->
            <div class="col-md-4">
                <a href="chat/chat.php" class="text-decoration-none">
                    <div class="card card-custom text-center p-4">
                        <div class="icon-box"><i class="bi bi-chat-dots-fill"></i></div>
                        <h5 class="card-title">Iniciar Conversa</h5>
                        <p class="card-text">Converse com usuários.</p>
                    </div>
                </a>
            </div>
            
        </div>
    </div>

</body>
</html>
