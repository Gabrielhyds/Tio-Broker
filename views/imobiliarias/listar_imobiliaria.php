<?php
// views/imobiliarias/listar.php
session_start();

// Verifica se o usuário está logado e se tem permissão
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['permissao'] !== 'SuperAdmin') {
    header('Location: /auth/login.php');
    exit;
}

$activeMenu = 'imobiliaria_listar';

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Imobiliaria.php';

$imobiliariaModel = new Imobiliaria($connection);

// --- NOVO: LÓGICA DE PAGINAÇÃO ---
$itens_por_pagina = 10; // Defina quantos itens você quer por página
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) {
    $pagina_atual = 1;
}

$total_itens = $imobiliariaModel->contarTotal();
$total_paginas = ceil($total_itens / $itens_por_pagina);

// Busca os dados paginados para a página atual
$lista = $imobiliariaModel->listarPaginado($pagina_atual, $itens_por_pagina);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Imobiliárias Cadastradas</title>

    <!-- Bootstrap 5 CSS + FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- CSS global -->
    <link href="/assets/css/styles.css" rel="stylesheet">
</head>

<body class="bg-light">

    <?php
// Inclui o dashboard
include_once '../dashboards/dashboard_superadmin.php';
?>

    <!-- Conteúdo Principal -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Título e ação -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Imobiliárias Cadastradas</h2>
                <a href="cadastrar_imobiliaria.php" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i> Nova Imobiliária
                </a>
            </div>

            <!-- Alertas de sucesso ou erro -->
            <?php if (isset($_SESSION['sucesso'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['sucesso']); unset($_SESSION['sucesso']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['erro'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['erro']); unset($_SESSION['erro']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
            <?php endif; ?>

            <!-- Card contendo a tabela -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="imobiliariaTable" class="table table-hover table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>CNPJ</th>
                                    <th>Usuários</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($lista)): ?>
                                <?php foreach ($lista as $item): ?>
                                <tr>
                                    <td><?= $item['id_imobiliaria'] ?></td>
                                    <td><?= htmlspecialchars($item['nome']) ?></td>
                                    <td><?= htmlspecialchars($item['cnpj']) ?></td>
                                    <td><span class="badge bg-primary"><?= $item['total_usuarios'] ?></span></td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="editar_imobiliaria.php?id=<?= $item['id_imobiliaria'] ?>"
                                                class="btn btn-outline-warning" title="Editar"><i
                                                    class="fas fa-pen"></i></a>
                                            <a href="../../controllers/ImobiliariaController.php?excluir=<?= $item['id_imobiliaria'] ?>"
                                                class="btn btn-outline-danger" title="Excluir"
                                                onclick="return confirm('Deseja realmente excluir esta imobiliária?')"><i
                                                    class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Nenhuma imobiliária encontrada.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- --- NOVO: CONTROLES DE PAGINAÇÃO --- -->
                    <?php if ($total_paginas > 1): ?>
                    <nav aria-label="Navegação da página">
                        <ul class="pagination justify-content-center mt-4">
                            <li class="page-item <?= $pagina_atual <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $pagina_atual - 1 ?>">Anterior</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <li class="page-item <?= $i == $pagina_atual ? 'active' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                            </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $pagina_atual >= $total_paginas ? 'disabled' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $pagina_atual + 1 ?>">Próxima</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>