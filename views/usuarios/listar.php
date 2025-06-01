<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: /auth/login.php');
    exit;
}

$activeMenu = 'usuario_listar';

require_once '../../config/config.php';
require_once '../../models/Usuario.php';

$usuario = new Usuario($connection);
$lista = $usuario->listarTodosComImobiliaria();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Usuários Cadastrados</title>

  <!-- Bootstrap 5 CSS + FontAwesome -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    rel="stylesheet"
  />

  <!-- CSS global (inclui estilos do sidebar) -->
  <link href="/assets/css/styles.css" rel="stylesheet" />
</head>
<body class="bg-light">

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

<main class="main-content">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Usuários Cadastrados</h2>
      <a href="cadastrar.php" class="btn btn-success">
        <i class="fas fa-plus me-1"></i> Novo Usuário
      </a>
    </div>

    <?php if (isset($_GET['sucesso'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        Usuário cadastrado com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
      </div>
    <?php elseif (isset($_GET['atualizado'])): ?>
      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        Usuário atualizado com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
      </div>
    <?php elseif (isset($_GET['excluido'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-trash-alt me-2"></i>
        Usuário excluído com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
      </div>
    <?php endif; ?>

    <div class="card shadow-sm">
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-4">
            <div class="input-group">
              <span class="input-group-text bg-white">
                <i class="fas fa-search text-secondary"></i>
              </span>
              <input
                type="text"
                id="searchInput"
                class="form-control"
                placeholder="Pesquisar usuário..."
                onkeyup="filterTable()"
              />
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table
            id="usuarioTable"
            class="table table-hover table-striped align-middle"
          >
            <thead class="table-light">
              <tr>
                <th scope="col">Nome</th>
                <th scope="col">Email</th>
                <th scope="col">Permissão</th>
                <th scope="col">Imobiliária</th>
                <th scope="col" class="text-center">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($lista)): ?>
                <?php foreach ($lista as $u): ?>
                  <tr>
                    <td><?= htmlspecialchars($u['nome'], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($u['email'], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($u['permissao'], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($u['nome_imobiliaria'] ?? '---', ENT_QUOTES) ?></td>
                    <td class="text-center">
                      <div class="btn-group btn-group-sm" role="group" aria-label="Ações">
                        <a
                          href="editar.php?id=<?= $u['id_usuario'] ?>"
                          class="btn btn-outline-warning"
                          title="Editar"
                        >
                          <i class="fas fa-pen"></i>
                        </a>
                        <a
                          href="../../controllers/UsuarioController.php?excluir=<?= $u['id_usuario'] ?>"
                          class="btn btn-outline-danger"
                          title="Excluir"
                          onclick="return confirm('Deseja realmente excluir este usuário?')"
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
                    Nenhum usuário encontrado.
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

<script
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>
<script>
  function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('#usuarioTable tbody tr');

    rows.forEach(row => {
      const nomeCell = row.querySelector('td:nth-child(1)');
      const emailCell = row.querySelector('td:nth-child(2)');
      const nomeText = nomeCell.textContent.toLowerCase();
      const emailText = emailCell.textContent.toLowerCase();
      // Pesquisa por nome ou email
      row.style.display =
        nomeText.includes(filter) || emailText.includes(filter) ? '' : 'none';
    });
  }
</script>
</body>
</html>