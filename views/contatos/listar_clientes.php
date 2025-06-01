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
    <title>Lista de Clientes - Tio Broker CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #eef2f5;
            color: #333;
        }
        .container-listagem {
            background-color: #ffffff;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .page-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .page-header h2 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 0; /* Remover margem inferior do h2 */
        }
        .page-header .btn-group-actions a { /* Estilo para o grupo de botões no cabeçalho */
            margin-left: 10px;
        }
        .table {
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 10px;
            width: 100%;
        }
        .table th, .table td {
            vertical-align: middle;
            padding: 0.8rem 0.7rem;
            border-bottom-width: 1px;
            font-size: 0.88rem;
        }
        .table th {
            white-space: nowrap;
            font-weight: 600;
            color: #495057;
        }
        .table-header-custom th {
            background-color: #f8f9fa;
            color: #343a40;
            border-top: 1px solid #dee2e6 !important;
            border-bottom: 2px solid #dee2e6 !important;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f3f5;
        }
        .action-buttons a {
             margin: 0;
        }
        .img-thumbnail-custom {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
        }
        .icon-placeholder {
            font-size: 1.6rem;
            color: #adb5bd;
        }
        .badge {
            font-size: 0.7rem;
            padding: 0.3em 0.55em;
            font-weight: 500;
        }
        .badge-potencial {
            background-color: rgba(25, 135, 84, 0.1);
            color: #0f5132;
            border: 1px solid rgba(25, 135, 84, 0.2);
        }
        .badge-nao-potencial {
            background-color: rgba(220, 53, 69, 0.1);
            color: #842029;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }
        .btn-action-details {
            padding: 0.25rem 0.6rem;
            font-size: 0.8rem;
        }
        .btn-primary { font-weight: 500; }
        .btn-outline-primary { font-weight: 500; } /* Adicionado para consistência */
        .alert { border-left-width: 4px; border-radius: 0.375rem; }
        .alert-success { border-left-color: #198754; }
        .alert-danger { border-left-color: #dc3545; }
        .alert-info { border-left-color: #0dcaf0; }

        @media (max-width: 768px) {
            .container-listagem { padding: 20px 15px; margin-top: 15px; margin-bottom: 15px; }
            .page-header { 
                flex-direction: column; 
                align-items: stretch !important; 
            }
            .page-header h2 { 
                text-align: center; 
                margin-bottom: 15px; 
            }
            .page-header .btn-group-actions { /* Grupo de botões em coluna */
                display: flex;
                flex-direction: column;
                gap: 10px; /* Espaço entre os botões empilhados */
            }
            .page-header .btn-group-actions a {
                margin-left: 0; /* Remove margem esquerda quando empilhado */
                width: 100%; /* Botões ocupam largura total */
            }
            .table th, .table td { font-size: 0.8rem; padding: 0.6rem 0.4rem; }
            .action-buttons a.btn-action-details { width: 100%; }
            .col-cpf, .col-empreendimento, .col-corretor, .col-imobiliaria, .col-dt-cadastro { display: none; }
        }
         @media (min-width: 769px) and (max-width: 992px) {
            .table th, .table td { font-size: 0.85rem; padding: 0.7rem 0.5rem; }
            .col-empreendimento, .col-dt-cadastro { display: none; }
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
    <div class="container-fluid px-md-4 px-lg-5">
        <div class="container-listagem">
            <div class="d-flex flex-wrap justify-content-between align-items-center page-header">
                <h2 class="me-sm-3"><i class="bi bi-people-fill"></i> Clientes Cadastrados</h2>
                <div class="btn-group-actions d-flex align-items-center">
                    <a href="<?= htmlspecialchars($dashboardUrl ?? '../../index.php') ?>" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-house-door-fill"></i> <span class="d-none d-sm-inline">Voltar ao Início</span>
                    </a>
                    <a href="index.php?controller=cliente&action=cadastrar" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle"></i> Novo Cliente
                    </a> 

                </div>
            </div>

            <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['mensagem_sucesso']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php unset($_SESSION['mensagem_sucesso']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['mensagem_erro'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($_SESSION['mensagem_erro']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php unset($_SESSION['mensagem_erro']); ?>
            <?php endif; ?>

            <?php if (empty($clientes)): ?>
                <div class="alert alert-info text-center py-4">
                    <i class="bi bi-journal-x fs-1 d-block mb-3"></i>
                    <h4 class="alert-heading">Nenhum Cliente Encontrado</h4>
                    <p class="mb-0">Parece que ainda não há clientes cadastrados no sistema.</p>
                    <hr>
                    <p class="mb-0"><a href="index.php?controller=cliente&action=cadastrar" class="btn btn-info text-white"><i class="bi bi-person-plus-fill"></i> Adicionar Primeiro Cliente</a></p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-header-custom">
                            <tr>
                                <th>Nome</th>
                                <th>Telefone</th>
                                <th class="col-cpf">CPF</th>
                                <th class="col-empreendimento">Empreendimento</th>
                                <th class="text-center">Classificação</th>
                                <th class="text-center">Foto</th>
                                <th class="col-corretor">Corretor</th>
                                <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['permissao'] === 'SuperAdmin'): ?>
                                    <th class="col-imobiliaria">Imobiliária</th>
                                <?php endif; ?>
                                <th class="col-dt-cadastro">Data Cadastro</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clientes as $cliente): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($cliente['nome']) ?></strong></td>
                                    <td><?= htmlspecialchars($cliente['numero']) ?></td>
                                    <td class="col-cpf"><?= htmlspecialchars($cliente['cpf'] ?? '-') ?></td>
                                    <td class="col-empreendimento"><?= htmlspecialchars($cliente['empreendimento'] ?? '-') ?></td>
                                    <td class="text-center">
                                        <?php
                                        $tipoListaClass = '';
                                        if ($cliente['tipo_lista'] === 'Potencial') {
                                            $tipoListaClass = 'badge-potencial';
                                        } elseif ($cliente['tipo_lista'] === 'Não potencial') {
                                            $tipoListaClass = 'badge-nao-potencial';
                                        }
                                        ?>
                                        <span class="badge <?= $tipoListaClass ?>"><?= htmlspecialchars($cliente['tipo_lista']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($cliente['foto'])): ?>
                                            <img src="<?= htmlspecialchars($cliente['foto']) ?>" class="img-thumbnail-custom" alt="Foto de <?= htmlspecialchars($cliente['nome']) ?>"
                                                 onerror="this.style.display='none'; this.parentElement.innerHTML = '<i class=\'bi bi-image-alt icon-placeholder\'></i><span class=\'d-block small text-danger\'>Link inválido</span>';">
                                        <?php else: ?>
                                            <i class="bi bi-camera-fill icon-placeholder"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td class="col-corretor"><?= htmlspecialchars($cliente['nome_corretor'] ?? 'N/A') ?></td>
                                    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['permissao'] === 'SuperAdmin'): ?>
                                        <td class="col-imobiliaria"><?= htmlspecialchars($cliente['nome_imobiliaria'] ?? 'N/A') ?></td>
                                    <?php endif; ?>
                                    <td class="col-dt-cadastro">
                                        <span class="d-block"><?= isset($cliente['criado_em']) ? date('d/m/Y', strtotime($cliente['criado_em'])) : '-' ?></span>
                                        <small class="text-muted"><?= isset($cliente['criado_em']) ? date('H:i', strtotime($cliente['criado_em'])) : '' ?></small>
                                    </td>
                                    <td class="text-center action-buttons">
                                        <a href="index.php?controller=cliente&action=mostrar&id_cliente=<?= $cliente['id_cliente'] ?>" class="btn btn-sm btn-outline-primary btn-action-details" title="Ver Detalhes do Cliente">
                                            <i class="bi bi-eye-fill"></i> <span class="d-none d-md-inline">Detalhes</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
