<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($cliente) || empty($cliente)) {
    $_SESSION['mensagem_erro'] = "Não foi possível carregar os dados do cliente.";
    header('Location: index.php?controller=cliente&action=listar');
    exit;
}
// A variável $interacoes e $documentos devem ser passadas pelo ClienteController
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
            padding: 30px 35px;
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
            width: 100px;
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
            font-size: 3rem;
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
            margin-bottom: 5px;
            font-size: 1.7rem;
        }
        .profile-header p {
            color: #6c757d;
            margin-bottom: 5px;
            font-size: 0.88rem;
        }
        .info-section, .history-section, .documents-section, .actions-section {
            margin-bottom: 30px; 
        }
        .info-section h5, .history-section h5, .documents-section h5, .actions-section h5 {
            color: #343a40;
            font-weight: 600;
            font-size: 1.05rem;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e9ecef;
            display: inline-block;
        }
        .actions-section .card-header h5 { /* Para o card de ações não ter a borda do título */
             border-bottom: none;
             margin-bottom: 0;
        }
        .info-section h5 i, .history-section h5 i, .documents-section h5 i, .actions-section h5 i {
            margin-right: 8px;
            color: #495057;
        }
        .info-item {
            margin-bottom: 12px;
            line-height: 1.5;
            display: flex;
            flex-wrap: wrap;
        }
        .info-item strong {
            color: #495057;
            padding-right: 8px;
            font-weight: 500;
            flex-shrink: 0;
        }
        .info-item span {
            word-break: break-word;
        }

        .history-entry {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-left-width: 4px;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .history-entry .meta {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 8px;
        }
        .history-entry .meta .badge {
            font-size: 0.75rem;
        }
        .history-entry p {
            margin-bottom: 0;
            line-height: 1.6;
            white-space: pre-wrap;
        }
        .border-primary-subtle { border-left-color: #0d6efd !important; }
        .border-success-subtle { border-left-color: #198754 !important; }
        .border-warning-subtle { border-left-color: #ffc107 !important; }
        .documents-section .card-header h5 {
            border-bottom: none;
            margin-bottom: 0;
            font-size: 1.15rem;
        }
        .form-container { /* Para os formulários que serão mostrados/ocultos */
            display: none; /* Começam ocultos */
        }

        .action-buttons-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .action-buttons-footer .btn-group-start a,
        .action-buttons-footer .btn-group-end a {
            margin-left: 8px;
            padding: 0.45rem 0.9rem;
            font-weight: 500;
            font-size: 0.9rem;
        }
        .action-buttons-footer .btn-group-start a:first-child,
        .action-buttons-footer .btn-group-end a:first-child {
            margin-left: 0;
        }

        @media (max-width: 991px) { 
            .info-item strong { min-width: 130px; }
        }

        @media (max-width: 768px) {
            .profile-header { flex-direction: column; text-align: center; }
            .profile-header img, .profile-header .icon-placeholder { margin-right: 0; margin-bottom: 15px; }
            .info-item { flex-direction: column; }
            .info-item strong { min-width: auto; margin-bottom: 3px; }
            .action-buttons-footer { flex-direction: column; }
            .action-buttons-footer .btn-group-start,
            .action-buttons-footer .btn-group-end {
                width: 100%; display: flex; flex-direction: column; margin-bottom: 10px;
            }
            .action-buttons-footer .btn-group-start a,
            .action-buttons-footer .btn-group-end a {
                width: 100%; margin-left: 0; margin-bottom: 8px;
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
    <div class="container">
        <div class="profile-container">
            <div class="d-flex justify-content-end align-items-center mb-3">
                <a href="index.php?controller=cliente&action=listar" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left-circle"></i> Voltar para Lista
                </a>
                <a href="index.php?controller=cliente&action=editar&id_cliente=<?= htmlspecialchars($cliente['id_cliente']) ?>" class="btn btn-sm btn-warning me-2">
                    <i class="bi bi-pencil-square"></i> Editar Cliente
                </a>
                <a href="index.php?controller=cliente&action=excluir&id_cliente=<?= htmlspecialchars($cliente['id_cliente']) ?>" class="btn btn-sm btn-danger"
                   onclick="return confirm('Tem certeza que deseja excluir o cliente \'<?= htmlspecialchars(addslashes($cliente['nome'])) ?>\'? Esta ação não pode ser desfeita.');">
                   <i class="bi bi-trash-fill"></i> Excluir Cliente
                </a>
            </div>
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
            <?php if (isset($_SESSION['mensagem_info'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['mensagem_info']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
            <?php unset($_SESSION['mensagem_info']); ?>
            <?php endif; ?>

            <div class="row g-4">
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
                        <div class="info-item"><strong>Renda:</strong> <span>R$ <?= isset($cliente['renda']) && $cliente['renda'] !== null ? number_format((float)$cliente['renda'], 2, ',', '.') : 'Não informada' ?></span></div>
                        <div class="info-item"><strong>Entrada:</strong> <span>R$ <?= isset($cliente['entrada']) && $cliente['entrada'] !== null ? number_format((float)$cliente['entrada'], 2, ',', '.') : 'Não informada' ?></span></div>
                        <div class="info-item"><strong>FGTS:</strong> <span>R$ <?= isset($cliente['fgts']) && $cliente['fgts'] !== null ? number_format((float)$cliente['fgts'], 2, ',', '.') : 'Não informado' ?></span></div>
                        <div class="info-item"><strong>Subsídio:</strong> <span>R$ <?= isset($cliente['subsidio']) && $cliente['subsidio'] !== null ? number_format((float)$cliente['subsidio'], 2, ',', '.') : 'Não informado' ?></span></div>
                    </section>
                </div>
                <div class="col-lg-4 col-md-12">
                    <section class="info-section">
                        <h5><i class="bi bi-briefcase-fill"></i> Informações do Corretor</h5>
                        <div class="info-item"><strong>Corretor:</strong> <span><?= htmlspecialchars($cliente['nome_corretor'] ?? 'Não associado') ?></span></div>
                        <div class="info-item"><strong>Email:</strong> <span><?= htmlspecialchars($cliente['email_corretor'] ?? 'Não informado') ?></span></div>
                        <div class="info-item"><strong>Telefone:</strong> <span><?= htmlspecialchars($cliente['telefone_corretor'] ?? 'Não informado') ?></span></div>
                        <div class="info-item"><strong>Imobiliária:</strong> <span><?= htmlspecialchars($cliente['nome_imobiliaria'] ?? 'Não associada') ?></span></div>
                    </section>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <section class="actions-section card shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                           <h5 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i>Adicionar Nova Ação</h5>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="dropdownAcoesCliente" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-pencil-fill me-1"></i> Selecionar Ação
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownAcoesCliente">
                                    <li><a class="dropdown-item" href="#" id="btnMostrarFormInteracao"><i class="bi bi-chat-left-text me-2"></i>Registrar Nova Interação</a></li>
                                    <li><a class="dropdown-item" href="#" id="btnMostrarFormDocumento"><i class="bi bi-file-earmark-arrow-up me-2"></i>Anexar Novo Documento</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-muted" href="#" id="btnEsconderFormularios"><i class="bi bi-x-circle me-2"></i>Fechar Formulário</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="formInteracaoContainer" class="form-container">
                                <h6 class="card-title mb-3"><i class="bi bi-chat-dots-fill me-2"></i>Registrar Nova Interação</h6>
                                <form method="POST" action="index.php?controller=interacao&action=adicionar">
                                    <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($cliente['id_cliente']) ?>">
                                    <div class="mb-3">
                                        <label for="tipo_interacao" class="form-label">Tipo de Interação:</label>
                                        <select name="tipo_interacao" id="tipo_interacao" class="form-select form-select-sm" required>
                                            <option value="mensagem" selected>Mensagem</option>
                                            <option value="telefone">Ligação Telefônica</option>
                                            <option value="reuniao">Reunião</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="descricao" class="form-label">Descrição / Mensagem:</label>
                                        <textarea name="descricao" id="descricao" class="form-control form-control-sm" rows="3" required placeholder="Digite os detalhes da interação..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-plus-lg"></i> Adicionar ao Histórico
                                    </button>
                                </form>
                            </div>

                            <div id="formDocumentoContainer" class="form-container">
                                <h6 class="card-title mb-3"><i class="bi bi-file-earmark-medical-fill me-2"></i>Anexar Novo Documento</h6>
                                <form method="POST" action="index.php?controller=documento&action=adicionar" enctype="multipart/form-data">
                                    <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($cliente['id_cliente']) ?>">
                                    <div class="mb-3">
                                        <label for="doc_nome_documento" class="form-label">Nome do Documento <span class="text-danger">*</span></label>
                                        <input type="text" name="nome_documento" id="doc_nome_documento" class="form-control form-control-sm" required placeholder="Ex: Contrato Assinado, RG Frente">
                                    </div>
                                    <div class="mb-3">
                                        <label for="doc_tipo_documento" class="form-label">Tipo do Documento <span class="text-danger">*</span></label>
                                        <input type="text" name="tipo_documento" id="doc_tipo_documento" class="form-control form-control-sm" required placeholder="Ex: PDF, Contrato, Identidade">
                                    </div>
                                    <div class="mb-3">
                                        <label for="doc_arquivo" class="form-label">Arquivo <span class="text-danger">*</span></label>
                                        <input type="file" name="arquivo_documento" id="doc_arquivo" class="form-control form-control-sm" required>
                                        <small class="form-text text-muted">Max: 10MB. Tipos: PDF, DOC, DOCX, JPG, PNG.</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-upload"></i> Anexar Documento
                                    </button>
                                </form>
                            </div>
                             <p id="placeholderAcoes" class="text-muted text-center py-3">Selecione uma ação no menu acima para começar.</p>
                        </div>
                    </section>
                </div>
            </div>


            <div class="row mt-4">
                <div class="col-12">
                    <section class="history-section">
                        <h5><i class="bi bi-list-ul me-2"></i>Histórico de Interações</h5>
                        <?php if (!isset($interacoes) || empty($interacoes)): ?>
                            <div class="alert alert-light text-center" role="alert">
                                <i class="bi bi-clock-history me-2"></i>Nenhuma interação registrada para este cliente ainda.
                            </div>
                        <?php else: ?>
                            <?php foreach ($interacoes as $interacao): ?>
                                <?php
                                    $borderClass = 'border-primary-subtle'; 
                                    $iconClass = 'bi-chat-dots-fill text-primary';
                                    $tipoLabel = 'Mensagem';
                                    switch ($interacao['tipo_interacao']) {
                                        case 'telefone':
                                            $borderClass = 'border-warning-subtle';
                                            $iconClass = 'bi-telephone-fill text-warning';
                                            $tipoLabel = 'Ligação';
                                            break;
                                        case 'reuniao':
                                            $borderClass = 'border-success-subtle';
                                            $iconClass = 'bi-people-fill text-success';
                                            $tipoLabel = 'Reunião';
                                            break;
                                    }
                                ?>
                                <div class="history-entry <?= $borderClass ?>">
                                    <div class="meta d-flex flex-wrap justify-content-between align-items-center">
                                        <div>
                                            <i class="bi <?= $iconClass ?> me-2"></i>
                                            <span class="badge bg-secondary me-2"><?= htmlspecialchars($tipoLabel) ?></span>
                                            Registrado por: <strong><?= htmlspecialchars($interacao['nome_usuario'] ?? 'Usuário Desconhecido') ?></strong>
                                        </div>
                                        <span class="text-nowrap"><?= date('d/m/Y H:i', strtotime($interacao['data_interacao'])) ?></span>
                                    </div>
                                    <p class="mt-2 mb-1"><?= nl2br(htmlspecialchars($interacao['descricao'])) ?></p>
                                     <?php if (!empty($interacao['anexo_interacao_nome_original']) && !empty($interacao['anexo_interacao_caminho'])): ?>
                                        <div class="mt-1">
                                            <small class="text-muted">Anexo da interação:
                                                <a href="<?= htmlspecialchars($interacao['anexo_interacao_caminho']) ?>" target="_blank" class="text-decoration-none">
                                                    <i class="bi bi-paperclip"></i> <?= htmlspecialchars($interacao['anexo_interacao_nome_original']) ?>
                                                </a>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </section>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <section class="documents-section">
                         <h5><i class="bi bi-folder2-open me-2"></i>Documentos Anexados</h5>
                        <?php if (!isset($documentos) || empty($documentos)): ?>
                            <div class="alert alert-light text-center" role="alert">
                                <i class="bi bi-folder-x me-2"></i>Nenhum documento anexado para este cliente ainda.
                            </div>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($documentos as $documento): ?>
                                    <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center py-3">
                                        <div class="me-3 mb-2 mb-md-0">
                                            <i class="bi bi-file-earmark-text me-2 fs-5 align-middle"></i>
                                            <strong class="align-middle"><?= htmlspecialchars($documento['nome_documento']) ?></strong>
                                            <span class="badge bg-info-subtle text-info-emphasis ms-2 align-middle"><?= htmlspecialchars($documento['tipo_documento']) ?></span>
                                            <br>
                                            <small class="text-muted ms-4 ps-1">
                                                Upload em: <?= isset($documento['data_upload']) ? date('d/m/Y H:i', strtotime($documento['data_upload'])) : 'Data não informada' ?>
                                                <?php if (isset($documento['nome_usuario_upload'])): ?>
                                                    por <?= htmlspecialchars($documento['nome_usuario_upload']) ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <div class="ms-md-auto">
                                             <a href="index.php?controller=documento&action=baixar&id_documento=<?= htmlspecialchars($documento['id_documento']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm" title="Ver/Baixar Documento">
                                                <i class="bi bi-download"></i> Ver / Baixar
                                            </a>
                                            <a href="index.php?controller=documento&action=excluir&id_documento=<?= $documento['id_documento'] ?>&id_cliente=<?= $cliente['id_cliente'] ?>" 
                                               class="btn btn-outline-danger btn-sm ms-2" 
                                               onclick="return confirm('Tem certeza que deseja excluir este documento: \'<?= htmlspecialchars(addslashes($documento['nome_documento'])) ?>\'? Esta ação não pode ser desfeita.');"
                                               title="Excluir Documento">
                                                <i class="bi bi-trash-fill"></i>
                                            </a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btnMostrarFormInteracao = document.getElementById('btnMostrarFormInteracao');
            const btnMostrarFormDocumento = document.getElementById('btnMostrarFormDocumento');
            const btnEsconderFormularios = document.getElementById('btnEsconderFormularios');

            const formInteracaoContainer = document.getElementById('formInteracaoContainer');
            const formDocumentoContainer = document.getElementById('formDocumentoContainer');
            const placeholderAcoes = document.getElementById('placeholderAcoes');

            function mostrarFormulario(formParaMostrar) {
                // Esconde todos os formulários e o placeholder
                formInteracaoContainer.style.display = 'none';
                formDocumentoContainer.style.display = 'none';
                placeholderAcoes.style.display = 'none';

                // Mostra o formulário selecionado
                if (formParaMostrar) {
                    formParaMostrar.style.display = 'block';
                } else {
                    // Se nenhum formulário for para mostrar (ex: fechar), mostra o placeholder
                    placeholderAcoes.style.display = 'block';
                }
            }

            btnMostrarFormInteracao.addEventListener('click', function (e) {
                e.preventDefault();
                mostrarFormulario(formInteracaoContainer);
            });

            btnMostrarFormDocumento.addEventListener('click', function (e) {
                e.preventDefault();
                mostrarFormulario(formDocumentoContainer);
            });

            btnEsconderFormularios.addEventListener('click', function(e) {
                e.preventDefault();
                mostrarFormulario(null); // Passa null para esconder todos e mostrar placeholder
            });

            // Inicialmente, mostra o placeholder
            mostrarFormulario(null);
        });
    </script>
</body>
</ht