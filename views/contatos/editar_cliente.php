<?php
@session_start();

// Se o usuário não estiver logado, envia para o login
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Verifica se a variável $cliente existe (passada pelo controller)
if (!isset($cliente) || empty($cliente)) {
    $_SESSION['mensagem_erro'] = "Não foi possível carregar os dados do cliente para edição.";
    // Idealmente, redirecionar para uma página de erro ou lista, se o ID não for válido na URL.
    // Este header() pode causar problemas se algo já foi enviado para o output.
    // O controller já deve ter feito essa checagem.
    if (!headers_sent()) {
        header('Location: index.php?controller=cliente&action=listar');
        exit;
    } else {
        echo "<div class='alert alert-danger'>Erro crítico: Dados do cliente não encontrados. Contate o suporte.</div>";
        // Poderia incluir um link de volta para a listagem aqui.
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente: <?= htmlspecialchars($cliente['nome']) ?> - Tio Broker CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #eef2f5;
        }
        .container-form {
            max-width: 850px;
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .form-label {
            font-weight: 500;
            color: #495057;
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
        }
        .input-group-text {
             background-color: #f8f9fa;
             border-right: none;
        }
        .form-control {
            border-left: none; /* Para campos com input-group-text à esquerda */
        }
        .form-control.no-prepend-style { /* Classe para inputs sem prepend */
            border-left: var(--bs-border-width) solid #dee2e6;
        }

        .btn-success { background-color: #198754; border-color: #198754; font-weight: 500;}
        .btn-success:hover { background-color: #157347; border-color: #146c43;}
        .btn-outline-secondary { color: #495057; border-color: #ced4da; font-weight: 500;}
        .btn-outline-secondary:hover { background-color: #f8f9fa; color: #2c3e50; border-color: #adb5bd;}
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
    <h2><i class="bi bi-pencil-square"></i> Editar Cliente</h2>

    <?php if (isset($_SESSION['mensagem_erro_form'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['mensagem_erro_form']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
        <?php unset($_SESSION['mensagem_erro_form']); ?>
    <?php endif; ?>

    <form method="POST" action="index.php?controller=cliente&action=editar&id_cliente=<?= htmlspecialchars($cliente['id_cliente']) ?>">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="nome" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                <input type="text" name="nome" id="nome" class="form-control no-prepend-style" value="<?= htmlspecialchars($cliente['nome'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label for="numero" class="form-label">Número de Telefone <span class="text-danger">*</span></label>
                <input type="text" name="numero" id="numero" class="form-control no-prepend-style" value="<?= htmlspecialchars($cliente['numero'] ?? '') ?>" placeholder="(XX) XXXXX-XXXX" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-5">
                <label for="cpf" class="form-label">CPF <span class="text-danger">*</span></label>
                <input type="text" name="cpf" id="cpf" class="form-control no-prepend-style" value="<?= htmlspecialchars($cliente['cpf'] ?? '') ?>" placeholder="000.000.000-00" required>
            </div>
            <div class="col-md-7">
                <label for="empreendimento" class="form-label">Empreendimento de Interesse</label>
                <input type="text" name="empreendimento" id="empreendimento" class="form-control no-prepend-style" value="<?= htmlspecialchars($cliente['empreendimento'] ?? '') ?>">
            </div>
        </div>

        <h5 class="mt-4 mb-3 text-muted" style="font-weight: 500;">Informações Financeiras</h5>

        <div class="row mb-3">
            <div class="col-md-6 col-lg-3 mb-3 mb-lg-0">
                <label for="renda" class="form-label">Renda (R$)</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" name="renda" id="renda" class="form-control" value="<?= htmlspecialchars($cliente['renda'] ?? '') ?>" placeholder="0,00">
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-3 mb-lg-0">
                <label for="entrada" class="form-label">Entrada (R$)</label>
                 <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" name="entrada" id="entrada" class="form-control" value="<?= htmlspecialchars($cliente['entrada'] ?? '') ?>" placeholder="0,00">
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-3 mb-md-0">
                <label for="fgts" class="form-label">FGTS (R$)</label>
                 <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" name="fgts" id="fgts" class="form-control" value="<?= htmlspecialchars($cliente['fgts'] ?? '') ?>" placeholder="0,00">
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <label for="subsidio" class="form-label">Subsídio (R$)</label>
                 <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" name="subsidio" id="subsidio" class="form-control" value="<?= htmlspecialchars($cliente['subsidio'] ?? '') ?>" placeholder="0,00">
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="foto" class="form-label">URL da Foto do Cliente</label>
            <div class="input-group">
                 <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                <input type="url" name="foto" id="foto" class="form-control" value="<?= htmlspecialchars($cliente['foto'] ?? '') ?>" placeholder="https://exemplo.com/imagem.jpg">
            </div>
             <?php if (!empty($cliente['foto'])): ?>
                <div class="mt-2">
                    <img src="<?= htmlspecialchars($cliente['foto']) ?>" alt="Foto atual" style="max-width: 100px; max-height: 100px; border-radius: 8px;"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <small class="text-danger" style="display:none;">Link da foto atual inválido.</small>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-4">
            <label for="tipo_lista" class="form-label">Classificação do Cliente <span class="text-danger">*</span></label>
            <select name="tipo_lista" id="tipo_lista" class="form-select no-prepend-style" required>
                <option value="Potencial" <?= ($cliente['tipo_lista'] ?? '') === 'Potencial' ? 'selected' : '' ?>>Potencial</option>
                <option value="Não potencial" <?= ($cliente['tipo_lista'] ?? '') === 'Não potencial' ? 'selected' : '' ?>>Não potencial</option>
            </select>
        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">
            <a href="index.php?controller=cliente&action=mostrar&id_cliente=<?= htmlspecialchars($cliente['id_cliente']) ?>" class="btn btn-lg btn-outline-secondary">
                <i class="bi bi-x-lg"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-lg btn-success">
                <i class="bi bi-check-lg"></i> Salvar Alterações
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
