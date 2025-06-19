<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['permissao'] !== 'SuperAdmin') {
  header('Location: /auth/login.php');
  exit;
}
$activeMenu = 'imobiliaria_listar';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Imobiliaria.php';
$imobiliariaModel = new Imobiliaria($connection);

// --- LÓGICA DE FILTRO E PAGINAÇÃO ---
$itens_por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$filtro = $_GET['filtro'] ?? '';

$total_itens = $imobiliariaModel->contarTotal($filtro);
$total_paginas = ceil($total_itens / $itens_por_pagina);

$lista = $imobiliariaModel->listarPaginado($pagina_atual, $itens_por_pagina, $filtro);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Imobiliárias Cadastradas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="/assets/css/styles.css" rel="stylesheet">
</head>

<body class="bg-light">

  <?php include_once '../dashboards/dashboard_superadmin.php'; ?>

  <main class="main-content">
    <div class="container-fluid">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Imobiliárias Cadastradas</h2>
        <a href="cadastrar_imobiliaria.php" class="btn btn-success"><i class="fas fa-plus me-1"></i> Nova
          Imobiliária</a>
      </div>

      <!-- Alertas -->
      <?php if (isset($_SESSION['sucesso'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($_SESSION['sucesso']);
          unset($_SESSION['sucesso']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
      <?php endif; ?>

      <div class="card shadow-sm">
        <div class="card-body">
          <!-- --- FORMULÁRIO DE FILTRO --- -->
          <form method="GET" action="listar_imobiliaria.php" class="mb-4">
            <div class="row">
              <div class="col-md-5">
                <div class="input-group">
                  <input type="text" name="filtro" class="form-control"
                    placeholder="Buscar por ID, nome ou CNPJ..."
                    value="<?= htmlspecialchars($filtro) ?>">
                  <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                  <a href="listar_imobiliaria.php" class="btn btn-outline-secondary"
                    title="Limpar filtro"><i class="fas fa-times"></i></a>
                </div>
              </div>
            </div>
          </form>

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
                        <div class="btn-group btn-group-sm">
                          <a href="editar_imobiliaria.php?id=<?= $item['id_imobiliaria'] ?>"
                            class="btn btn-outline-warning" title="Editar"><i
                              class="fas fa-pen"></i></a>
                          <a href="../../controllers/ImobiliariaController.php?excluir=<?= $item['id_imobiliaria'] ?>"
                            class="btn btn-outline-danger" title="Excluir"
                            onclick="return confirm('Deseja realmente excluir?')"><i
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

          <!-- --- PAGINAÇÃO COM FILTRO --- -->
          <?php if ($total_paginas > 1): ?>
            <nav>
              <ul class="pagination justify-content-center mt-4">
                <li class="page-item <?= $pagina_atual <= 1 ? 'disabled' : '' ?>">
                  <a class="page-link"
                    href="?pagina=<?= $pagina_atual - 1 ?>&filtro=<?= urlencode($filtro) ?>">Anterior</a>
                </li>
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                  <li class="page-item <?= $i == $pagina_atual ? 'active' : '' ?>">
                    <a class="page-link"
                      href="?pagina=<?= $i ?>&filtro=<?= urlencode($filtro) ?>"><?= $i ?></a>
                  </li>
                <?php endfor; ?>
                <li class="page-item <?= $pagina_atual >= $total_paginas ? 'disabled' : '' ?>">
                  <a class="page-link"
                    href="?pagina=<?= $pagina_atual + 1 ?>&filtro=<?= urlencode($filtro) ?>">Próxima</a>
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