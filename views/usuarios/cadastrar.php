<?php

@session_start();

// Se o usuário não estiver logado, envia para o login
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}
require_once '../../config/config.php';
require_once '../../models/Imobiliaria.php';

$imobiliaria = new Imobiliaria($connection);
$listaImobiliarias = $imobiliaria->listarTodas();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f2f4f7;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        h2 {
            font-weight: 600;
            color: #333;
        }
        label {
            font-weight: 500;
            color: #555;
        }
    </style>
</head>
<body>
    <?php
        if ($_SESSION['usuario']['permissao'] === 'SuperAdmin') {
            include_once '../dashboards/dashboard_superadmin.php';
        } elseif ($_SESSION['usuario']['permissao'] === 'Admin') {
            include_once '../dashboards/dashboard_admin.php';
        } elseif ($_SESSION['usuario']['permissao'] === 'Coordenador') {
            include_once '../dashboards/dashboard_coordenador.php';
        } else {
            include_once '../dashboards/dashboard_corretor.php';
        }
    ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card p-4">
                    <h2 class="mb-4 text-center"><i class="bi bi-person-plus-fill me-2"></i>Cadastrar Novo Usuário</h2>
                    <form action="../../controllers/UsuarioController.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="cadastrar">

                        <div class="mb-3">
                            <label>Nome:</label>
                            <input type="text" name="nome" class="form-control" placeholder="Nome completo" required>
                        </div>
                        <div class="mb-3">
                            <label>Email:</label>
                            <input type="email" name="email" class="form-control" placeholder="email@exemplo.com" required>
                        </div>
                        <div class="mb-3">
                            <label>CPF:</label>
                            <input type="text" name="cpf" class="form-control" placeholder="000.000.000-00" required>
                        </div>
                        <div class="mb-3">
                            <label>Telefone:</label>
                            <input type="text" name="telefone" class="form-control" placeholder="(99) 99999-9999" required>
                        </div>
                        <div class="mb-3">
                            <label>Senha:</label>
                            <input type="password" name="senha" class="form-control" placeholder="********" required>
                        </div>
                        <div class="mb-3">
                            <label>CRECI:</label>
                            <input type="text" name="creci" class="form-control" placeholder="Número do CRECI">
                        </div>
                        <div class="mb-3">
                            <label>Foto (opcional):</label>
                            <input type="file" name="foto" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Permissão:</label>
                            <select name="permissao" class="form-select" required>
                                <option value="Admin">Admin</option>
                                <option value="Coordenador">Coordenador</option>
                                <option value="Corretor">Corretor</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label>Imobiliária:</label>
                            <select name="id_imobiliaria" class="form-select" required>
                                <?php foreach ($listaImobiliarias as $i): ?>
                                    <option value="<?= $i['id_imobiliaria'] ?>"><?= $i['nome'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i> Cadastrar
                            </button>
                            <a href="listar.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Voltar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
