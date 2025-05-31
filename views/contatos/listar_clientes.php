<?php
// Inicia a sessão se ainda não foi iniciada, para exibir mensagens
if (session_status() == PHP_SESSION_NONE) {
    session_start();
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
            background-color: #eef2f5; /* Um tom de cinza ainda mais suave e moderno */
            color: #333;
        }
        /* Usando container-fluid para ocupar mais espaço, com padding customizado */
        .container-listagem {
            background-color: #ffffff;
            padding: 25px 30px; /* Padding interno do card */
            border-radius: 12px; /* Bordas mais arredondadas */
            box-shadow: 0 8px 25px rgba(0,0,0,0.08); /* Sombra mais sutil e moderna */
            margin-top: 20px; /* Reduzido o margin-top para container-fluid */
            margin-bottom: 20px;
        }
        .page-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .page-header h2 {
            color: #2c3e50; /* Azul escuro para o título */
            font-weight: 600;
        }
        .table {
            border-collapse: separate; /* Permite border-radius nas células da tabela */
            border-spacing: 0;
            margin-top: 10px; /* Espaço acima da tabela */
            width: 100%; /* Garante que a tabela tente ocupar a largura disponível */
        }
        .table th, .table td {
            vertical-align: middle;
            padding: 0.9rem 0.75rem; /* Padding interno das células */
            border-bottom-width: 1px;
            font-size: 0.9rem; /* Tamanho de fonte base para células */
        }
        .table th {
            white-space: nowrap;
            font-weight: 600; /* Cabeçalho mais destacado */
            color: #495057;
        }
        .table-header-custom th {
            background-color: #f8f9fa; /* Cabeçalho da tabela com cor suave */
            color: #343a40;
            border-top: 1px solid #dee2e6 !important;
            border-bottom: 2px solid #dee2e6 !important; /* Linha inferior mais grossa */
        }
        .table-hover tbody tr:hover {
            background-color: #f1f3f5; /* Hover suave nas linhas */
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .action-buttons a, .action-buttons button {
            margin: 0 3px; /* Espaçamento horizontal entre botões */
        }
        .img-thumbnail-custom {
            width: 45px; /* Reduzido para economizar espaço */
            height: 45px;
            object-fit: cover; /* Garante que a imagem cubra o espaço sem distorcer */
            border-radius: 8px; /* Bordas arredondadas para a imagem */
            border: 1px solid #e0e0e0; /* Borda mais sutil */
        }
        .icon-placeholder {
            font-size: 1.8rem; /* Reduzido */
            color: #adb5bd;
        }
        .badge { /* Estilo base para badges */
            font-size: 0.75rem; /* Reduzido */
            padding: 0.35em 0.6em;
            font-weight: 500;
        }
        .badge-potencial {
            background-color: rgba(25, 135, 84, 0.1);
            color: #0f5132;
            border: 1px solid rgba(25, 135, 84, 0.3);
        }
        .badge-nao-potencial {
            background-color: rgba(220, 53, 69, 0.1);
            color: #842029;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }
        .btn-action { /* Classe para botões de ação para consistência */
            padding: 0.25rem 0.5rem; /* Reduzido */
            font-size: 0.8rem; /* Reduzido */
        }
        .btn-primary {
            background-color: #0d6efd; border-color: #0d6efd;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: #0b5ed7; border-color: #0a58ca;
        }
        .btn-warning {
            background-color: #ffc107; border-color: #ffc107; color: #212529;
            font-weight: 500;
        }
         .btn-warning:hover {
            background-color: #e0a800; border-color: #d39e00;
        }
        .btn-danger {
            background-color: #dc3545; border-color: #dc3545;
            font-weight: 500;
        }
        .btn-danger:hover {
            background-color: #bb2d3b; border-color: #b02a37;
        }
        .alert { /* Estilo para alertas */
            border-left-width: 4px;
            border-radius: 0.375rem; /* Bootstrap 5 default */
        }
        .alert-success { border-left-color: #198754; }
        .alert-danger { border-left-color: #dc3545; }
        .alert-info { border-left-color: #0dcaf0; }

        /* Responsividade */
        @media (max-width: 768px) {
            .container-listagem {
                padding: 20px 15px; /* Menos padding em telas pequenas */
                margin-top: 15px;
                margin-bottom: 15px;
            }
            .page-header {
                flex-direction: column;
                align-items: stretch !important; /* Botão ocupa largura total */
            }
            .page-header h2 {
                text-align: center;
                margin-bottom: 15px; /* Espaço entre título e botão */
            }
            .page-header a.btn {
                /* width: 100%; Botão já é stretch */
            }
            .table th, .table td {
                 font-size: 0.85rem; /* Reduzir mais a fonte em telas pequenas */
                 padding: 0.7rem 0.5rem;
            }
             .action-buttons { /* Botões em coluna */
                display: flex;
                flex-direction: column;
                align-items: stretch; /* Estica os botões */
            }
            .action-buttons a.btn-action {
                width: 100%;
                margin-bottom: 5px;
                margin-left: 0;
                margin-right: 0;
            }
             .action-buttons a.btn-action:last-child {
                margin-bottom: 0;
            }
        }
         @media (min-width: 769px) and (max-width: 992px) {
            .table th, .table td {
                 font-size: 0.88rem;
                 padding: 0.8rem 0.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid px-md-4 px-lg-5">
        <div class="container-listagem">
            <div class="d-flex flex-wrap justify-content-between align-items-center page-header">
                <h2 class="mb-0 me-sm-3"><i class="bi bi-people-fill"></i> Clientes Cadastrados</h2>
                <a href="index.php?controller=cliente&action=cadastrar" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle"></i> Novo Cliente
                </a>
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
                                <th>CPF</th>
                                <th>Empreendimento</th>
                                <th>Renda</th>
                                <th>Entrada</th>
                                <th>FGTS</th>
                                <th>Subsídio</th>
                                <th class="text-center">Classificação</th>
                                <th class="text-center">Foto</th>
                                <th>Corretor</th>
                                <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['permissao'] === 'SuperAdmin'): ?>
                                    <th>Imobiliária</th>
                                <?php endif; ?>
                                <th>Data Cadastro</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clientes as $cliente): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($cliente['nome']) ?></strong></td>
                                    <td><?= htmlspecialchars($cliente['numero']) ?></td>
                                    <td><?= htmlspecialchars($cliente['cpf'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($cliente['empreendimento'] ?? '-') ?></td>
                                    <td>R$ <?= $cliente['renda'] ? number_format($cliente['renda'], 2, ',', '.') : '-' ?></td>
                                    <td>R$ <?= $cliente['entrada'] ? number_format($cliente['entrada'], 2, ',', '.') : '-' ?></td>
                                    <td>R$ <?= $cliente['fgts'] ? number_format($cliente['fgts'], 2, ',', '.') : '-' ?></td>
                                    <td>R$ <?= $cliente['subsidio'] ? number_format($cliente['subsidio'], 2, ',', '.') : '-' ?></td>
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
                                    <td><?= htmlspecialchars($cliente['nome_corretor'] ?? 'N/A') ?></td>
                                    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['permissao'] === 'SuperAdmin'): ?>
                                        <td><?= htmlspecialchars($cliente['nome_imobiliaria'] ?? 'N/A') ?></td>
                                    <?php endif; ?>
                                    <td>
                                        <span class="d-block"><?= isset($cliente['criado_em']) ? date('d/m/Y', strtotime($cliente['criado_em'])) : '-' ?></span>
                                        <small class="text-muted"><?= isset($cliente['criado_em']) ? date('H:i', strtotime($cliente['criado_em'])) : '' ?></small>
                                    </td>
                                    <td class="text-center action-buttons">
                                        <a href="index.php?controller=cliente&action=editar&id_cliente=<?= $cliente['id_cliente'] ?>" class="btn btn-sm btn-warning btn-action" title="Editar Cliente">
                                            <i class="bi bi-pencil-square"></i> <span class="d-none d-lg-inline">Editar</span>
                                        </a>
                                        <a href="index.php?controller=cliente&action=excluir&id_cliente=<?= $cliente['id_cliente'] ?>"
                                           class="btn btn-sm btn-danger btn-action" title="Excluir Cliente"
                                           onclick="return confirm('Tem certeza que deseja excluir o cliente \'<?= htmlspecialchars(addslashes($cliente['nome'])) ?>\'? Esta ação não pode ser desfeita.');">
                                           <i class="bi bi-trash-fill"></i> <span class="d-none d-lg-inline">Excluir</span>
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
