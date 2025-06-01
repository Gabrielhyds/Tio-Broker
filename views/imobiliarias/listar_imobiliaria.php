<?php
// views/imobiliarias/listar.php
session_start();

// Verifica se o usuário está logado e se tem permissão
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['permissao'] !== 'SuperAdmin') {
    header('Location: /auth/login.php');
    exit;
}

// Define qual menu ficará ativo no sidebar
$activeMenu = 'imobiliaria_listar';

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Imobiliaria.php';

$imobiliaria = new Imobiliaria($connection);
$lista       = $imobiliaria->listarTodas();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Imobiliárias Cadastradas</title>

  <!-- Bootstrap 5 CSS + FontAwesome -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    rel="stylesheet"
  >

  <!-- CSS global (inclui estilos do sidebar) -->
  <link href="/assets/css/styles.css" rel="stylesheet">
</head>
<body class="bg-light">

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

  <!-- Conteúdo Principal -->
  <main class="main-content">
    <div class="container-fluid">
      <!-- Título e ação -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Imobiliárias Cadastradas</h2>
        <a href="/imobiliarias/cadastrar.php" class="btn btn-success">
          <i class="fas fa-plus me-1"></i> Nova Imobiliária
        </a>
      </div>

      <!-- Alertas de ação -->
      <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="fas fa-check-circle me-2"></i>
          Imobiliária cadastrada com sucesso!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
      <?php elseif (isset($_GET['atualizado'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
          <i class="fas fa-info-circle me-2"></i>
          Imobiliária atualizada com sucesso!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
      <?php elseif (isset($_GET['excluido'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="fas fa-trash-alt me-2"></i>
          Imobiliária excluída com sucesso!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
      <?php endif; ?>

      <!-- Card contendo a tabela -->
      <div class="card shadow-sm">
        <div class="card-body">
          <!-- Campo de busca (funcionalidade futura) -->
          <div class="row mb-3">
            <div class="col-md-4">
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-search text-secondary"></i></span>
                <input
                  type="text"
                  id="searchInput"
                  class="form-control"
                  placeholder="Pesquisar imobiliária..."
                  onkeyup="filterTable()"
                >
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table
              id="imobiliariaTable"
              class="table table-hover table-striped align-middle"
            >
              <thead class="table-light">
                <tr>
                  <th scope="col">ID</th>
                  <th scope="col">Nome</th>
                  <th scope="col">CNPJ</th>
                  <th scope="col">Usuários</th>
                  <th scope="col" class="text-center">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($lista)): ?>
                  <?php foreach ($lista as $item): ?>
                    <tr>
                      <td><?= $item['id_imobiliaria'] ?></td>
                      <td><?= htmlspecialchars($item['nome'], ENT_QUOTES) ?></td>
                      <td><?= htmlspecialchars($item['cnpj'], ENT_QUOTES) ?></td>
                      <td>
                        <span class="badge bg-primary">
                          <?= $item['total_usuarios'] ?>
                        </span>
                      </td>
                      <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group">
                          <a
                            href="editar_imobiliaria.php?id=<?= $item['id_imobiliaria'] ?>"
                            class="btn btn-outline-warning"
                            title="Editar"
                          >
                            <i class="fas fa-pen"></i>
                          </a>
                          <a
                            href="../../controllers/ImobiliariaController.php?excluir=<?= $item['id_imobiliaria'] ?>"
                            class="btn btn-outline-danger"
                            title="Excluir"
                            onclick="return confirm('Deseja realmente excluir esta imobiliária?')"
                          >
                            <i class="fas fa-trash"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center text-muted">
                      Nenhuma imobiliária encontrada.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Bootstrap 5 JS Bundle (Popper + JS) -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
  ></script>
  <!-- Script para filtrar tabela -->
  <script>
    function filterTable() {
      const input = document.getElementById('searchInput');
      const filter = input.value.toLowerCase();
      const rows = document.querySelectorAll('#imobiliariaTable tbody tr');

      rows.forEach(row => {
        const nomeCell = row.querySelector('td:nth-child(2)');
        const nomeText = nomeCell.textContent.toLowerCase();
        row.style.display = nomeText.includes(filter) ? '' : 'none';
      });
    }
  </script>
</body>
</html>
