<?php
@session_start();

// Se o usuário não estiver logado, envia para o login
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Novo Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa; /* Um cinza claro para o fundo */
        }
        .container-form {
            max-width: 800px; /* Limita a largura para melhor legibilidade em telas maiores */
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .form-label {
            font-weight: 500;
        }
        .btn-success {
            background-color: #198754;
            border-color: #198754;
        }
        .btn-success:hover {
            background-color: #157347;
            border-color: #146c43;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        h2 {
            color: #343a40; /* Cor escura para o título */
            margin-bottom: 25px;
            text-align: center;
        }
        .input-group-text {
             background-color: #e9ecef;
        }
    </style>
</head>
<body>


<?php
//incluir o dashboard de acordo com o perfil do usuário
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

<div class="container container-form">
    <h2><i class="bi bi-person-plus-fill"></i> Cadastrar Novo Cliente</h2>

    <form method="POST" action="index.php?controller=cliente&action=cadastrar">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="nome" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                <input type="text" name="nome" id="nome" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="numero" class="form-label">Número de Telefone <span class="text-danger">*</span></label>
                <input type="text" name="numero" id="numero" class="form-control" placeholder="(XX) XXXXX-XXXX" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-5">
                <label for="cpf" class="form-label">CPF <span class="text-danger">*</span></label>
                <input type="text" name="cpf" id="cpf" class="form-control" placeholder="000.000.000-00" required>
                 </div>
            <div class="col-md-7">
                <label for="empreendimento" class="form-label">Empreendimento de Interesse</label>
                <input type="text" name="empreendimento" id="empreendimento" class="form-control">
            </div>
        </div>

        <h5 class="mt-4 mb-3 text-muted">Informações Financeiras (Opcional)</h5>

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="renda" class="form-label">Renda (R$)</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" name="renda" id="renda" class="form-control" placeholder="0,00">
                </div>
            </div>
            <div class="col-md-3">
                <label for="entrada" class="form-label">Entrada (R$)</label>
                 <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" name="entrada" id="entrada" class="form-control" placeholder="0,00">
                </div>
            </div>
            <div class="col-md-3">
                <label for="fgts" class="form-label">FGTS (R$)</label>
                 <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" name="fgts" id="fgts" class="form-control" placeholder="0,00">
                </div>
            </div>
            <div class="col-md-3">
                <label for="subsidio" class="form-label">Subsídio (R$)</label>
                 <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" name="subsidio" id="subsidio" class="form-control" placeholder="0,00">
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="foto" class="form-label">URL da Foto do Cliente</label>
            <div class="input-group">
                 <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                <input type="url" name="foto" id="foto" class="form-control" placeholder="https://exemplo.com/imagem.jpg">
            </div>
        </div>

        <div class="mb-4">
            <label for="tipo_lista" class="form-label">Classificação do Cliente <span class="text-danger">*</span></label>
            <select name="tipo_lista" id="tipo_lista" class="form-select" required>
                <option value="Potencial" selected>Potencial</option>
                <option value="Não potencial">Não potencial</option>
                </select>
        </div>

        <hr>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="index.php?controller=cliente&action=listar" class="btn btn-secondary me-md-2">
                <i class="bi bi-x-circle"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-lg"></i> Cadastrar Cliente
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
