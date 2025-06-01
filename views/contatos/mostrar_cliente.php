<?php
@session_start();

// Se o usuário não estiver logado, envia para o login
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Verifica se a variável $cliente existe (passada pelo controller)
if (!isset($cliente) || empty($cliente)) {
    $_SESSION['mensagem_erro'] = "Não foi possível carregar os dados do cliente.";
    header('Location: index.php?controller=cliente&action=listar');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Cliente: <?= htmlspecialchars($cliente['nome']) ?> - Tio Broker CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #eef2f5;
            color: #343a40;
            font-size: 0.95rem;
        }
        .profile-container {
            background-color: #ffffff;
            padding: 30px 35px; /* Ajustado padding */
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.07);
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 1px solid #e9ecef;
        }
        .profile-header img {
            width: 100px; /* Reduzido um pouco para equilíbrio */
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 20px;
            border: 3px solid #f8f9fa;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .profile-header .icon-placeholder {
            width: 100px;
            height: 100px;
            font-size: 3rem; /* Reduzido um pouco */
            border-radius: 50%;
            margin-right: 20px;
            background-color: #f8f9fa;
            color: #adb5bd;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #f8f9fa;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .profile-header h2 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 5px; /* Reduzido */
            font-size: 1.7rem; /* Ajustado */
        }
        .profile-header p {
            color: #6c757d;
            margin-bottom: 5px; /* Reduzido */
            font-size: 0.88rem; /* Ajustado */
        }
        .info-section {
            margin-bottom: 25px; /* Espaço entre seções */
        }
        .info-section h5 {
            color: #343a40;
            font-weight: 600;
            font-size: 1.05rem; /* Ajustado */
            margin-bottom: 15px; /* Ajustado */
            padding-bottom: 8px;
            border-bottom: 2px solid #e9ecef;
            display: inline-block;
        }
        .info-section h5 i {
            margin-right: 8px;
            color: #495057;
        }
        .info-item {
            margin-bottom: 12px; /* Ajustado */
            line-height: 1.5;
            display: flex; /* Para alinhar strong e valor */
            flex-wrap: wrap; /* Permite quebra se necessário */
        }
        .info-item strong {
            color: #495057;
            /* min-width removido para flexibilidade, usando padding */
            padding-right: 8px; /* Espaço entre label e valor */
            font-weight: 500;
            flex-shrink: 0; /* Evita que o label encolha */
        }
        .info-item span { /* Onde o valor é exibido */
            word-break: break-word; /* Quebra palavras longas */
        }

        .action-buttons-footer {
            margin-top: 30px; /* Ajustado */
            padding-top: 20px; /* Ajustado */
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .action-buttons-footer .btn-group-start a,
        .action-buttons-footer .btn-group-end a {
            margin-left: 8px;
            padding: 0.45rem 0.9rem; /* Ajustado padding dos botões */
            font-weight: 500;
            font-size: 0.9rem; /* Ajustado tamanho da fonte dos botões */
        }
        .action-buttons-footer .btn-group-start a:first-child,
        .action-buttons-footer .btn-group-end a:first-child {
            margin-left: 0;
        }

        @media (max-width: 991px) { /* Ajuste para tablets e menores */
            .info-item strong {
                 min-width: 130px; /* Para manter algum alinhamento em tablet */
            }
        }

        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            .profile-header img, .profile-header .icon-placeholder {
                margin-right: 0;
                margin-bottom: 15px; /* Ajustado */
            }
            .info-item {
                flex-direction: column; /* Labels acima dos valores em mobile */
            }
            .info-item strong {
                min-width: auto; /* Remove min-width em mobile */
                margin-bottom: 3px; /* Pequeno espaço entre label e valor */
            }
            .action-buttons-footer {
                flex-direction: column;
            }
            .action-buttons-footer .btn-group-start,
            .action-buttons-footer .btn-group-end {
                width: 100%;
                display: flex;
                flex-direction: column;
                margin-bottom: 10px;
            }
             .action-buttons-footer .btn-group-start a,
             .action-buttons-footer .btn-group-end a {
                width: 100%;
                margin-left: 0;
                margin-bottom: 8px; /* Ajustado */
             }
        }
        .badge { font-size: 0.8rem; padding: 0.4em 0.7em; font-weight: 500; }
        .badge-potencial { background-color: rgba(25, 135, 84, 0.1); color: #0f5132; border: 1px solid rgba(25, 135, 84, 0.3); }
        .badge-nao-potencial { background-color: rgba(220, 53, 69, 0.1); color: #842029; border: 1px solid rgba(220, 53, 69, 0.3); }
        
        .btn-outline-secondary { color: #495057; border-color: #ced4da; }
        .btn-outline-secondary:hover { background-color: #f8f9fa; color: #2c3e50; border-color: #adb5bd;}
        .btn-warning { background-color: #ffc107; border-color: #ffc107; color: #212529;}
        .btn-warning:hover { background-color: #e0a800; border-color: #d39e00;}
        .btn-danger { background-color: #dc3545; border-color: #dc3545;}
        .btn-danger:hover { background-color: #bb2d3b; border-color: #b02a37;}

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
    <div class="container">
        <div class="profile-container">
            <div class="profile-header">
                <?php if (!empty($cliente['foto'])): ?>
                    <img src="<?= htmlspecialchars($cliente['foto']) ?>" alt="Foto de <?= htmlspecialchars($cliente['nome']) ?>"
                         onerror="this.style.display='none'; document.getElementById('icon_placeholder_cliente').style.display='flex';">
                    <div id="icon_placeholder_cliente" class="icon-placeholder" style="display:none;"><i class="bi bi-person-bounding-box"></i></div>
                <?php else: ?>
                    <div id="icon_placeholder_cliente" class="icon-placeholder"><i class="bi bi-person-bounding-box"></i></div>
                <?php endif; ?>
                <div>
                    <h2><?= htmlspecialchars($cliente['nome']) ?></h2>
                    <p class="text-muted">ID do Cliente: #<?= htmlspecialchars($cliente['id_cliente']) ?></p>
                    <span class="badge <?= $cliente['tipo_lista'] === 'Potencial' ? 'badge-potencial' : 'badge-nao-potencial' ?>">
                        <i class="bi <?= $cliente['tipo_lista'] === 'Potencial' ? 'bi-check-circle' : 'bi-x-circle' ?>"></i> <?= htmlspecialchars($cliente['tipo_lista']) ?>
                    </span>
                </div>
            </div>

            <?php if (isset($_SESSION['mensagem_info'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['mensagem_info']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
            <?php unset($_SESSION['mensagem_info']); ?>
            <?php endif; ?>

            <div class="row g-4"> <!--{/* g-4 adiciona espaçamento entre as colunas */}-->
                <div class="col-lg-4 col-md-6">
                    <section class="info-section">
                        <h5><i class="bi bi-person-vcard"></i> Informações Pessoais</h5>
                        <div class="info-item"><strong>Telefone:</strong> <span><?= htmlspecialchars($cliente['numero']) ?></span></div>
                        <div class="info-item"><strong>CPF:</strong> <span><?= htmlspecialchars($cliente['cpf']) ?></span></div>
                        <div class="info-item"><strong>Empreendimento:</strong> <span><?= htmlspecialchars($cliente['empreendimento'] ?? 'Não informado') ?></span></div>
                         <div class="info-item"><strong>Cadastro:</strong> <span><?= isset($cliente['criado_em']) ? date('d/m/Y \à\s H:i', strtotime($cliente['criado_em'])) : 'Não informada' ?></span></div>
                    </section>
                </div>
                <div class="col-lg-4 col-md-6">
                    <section class="info-section">
                        <h5><i class="bi bi-cash-coin"></i> Informações Financeiras</h5>
                        <div class="info-item"><strong>Renda:</strong> <span>R$ <?= $cliente['renda'] ? number_format($cliente['renda'], 2, ',', '.') : 'Não informada' ?></span></div>
                        <div class="info-item"><strong>Entrada:</strong> <span>R$ <?= $cliente['entrada'] ? number_format($cliente['entrada'], 2, ',', '.') : 'Não informada' ?></span></div>
                        <div class="info-item"><strong>FGTS:</strong> <span>R$ <?= $cliente['fgts'] ? number_format($cliente['fgts'], 2, ',', '.') : 'Não informado' ?></span></div>
                        <div class="info-item"><strong>Subsídio:</strong> <span>R$ <?= $cliente['subsidio'] ? number_format($cliente['subsidio'], 2, ',', '.') : 'Não informado' ?></span></div>
                    </section>
                </div>
                 <div class="col-lg-4 col-md-12"> <!--{/* Em telas médias, esta seção ocupa a largura total abaixo das outras duas */}-->
                    <section class="info-section">
                        <h5><i class="bi bi-briefcase-fill"></i> Informações do Corretor</h5>
                        <div class="info-item"><strong>Corretor:</strong> <span><?= htmlspecialchars($cliente['nome_corretor'] ?? 'Não associado') ?></span></div>
                        <div class="info-item"><strong>Email:</strong> <span><?= htmlspecialchars($cliente['email_corretor'] ?? 'Não informado') ?></span></div>
                        <div class="info-item"><strong>Telefone:</strong> <span><?= htmlspecialchars($cliente['telefone_corretor'] ?? 'Não informado') ?></span></div>
                        <div class="info-item"><strong>Imobiliária:</strong> <span><?= htmlspecialchars($cliente['nome_imobiliaria'] ?? 'Não associada') ?></span></div>
                    </section>
                </div>
            </div>


            <div class="action-buttons-footer">
                <div class="btn-group-start">
                    <a href="index.php?controller=cliente&action=listar" class="btn btn-lg btn-outline-secondary">
                        <i class="bi bi-arrow-left-circle"></i> Voltar para Lista
                    </a>
                </div>
                <div class="btn-group-end">
                    <a href="index.php?controller=cliente&action=editar&id_cliente=<?= $cliente['id_cliente'] ?>" class="btn btn-lg btn-warning">
                        <i class="bi bi-pencil-square"></i> Editar Cliente
                    </a>
                    <a href="index.php?controller=cliente&action=excluir&id_cliente=<?= $cliente['id_cliente'] ?>" class="btn btn-lg btn-danger"
                       onclick="return confirm('Tem certeza que deseja excluir o cliente \'<?= htmlspecialchars(addslashes($cliente['nome'])) ?>\'? Esta ação não pode ser desfeita.');">
                       <i class="bi bi-trash-fill"></i> Excluir Cliente
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
