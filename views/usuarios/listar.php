<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
  header('Location: /auth/login.php');
  exit;
}

// Define o menu ativo para o sidebar
$activeMenu = 'usuario_listar';

require_once '../../config/config.php';
require_once '../../models/Usuario.php';

$usuarioModel = new Usuario($connection);

// --- NOVO: LÓGICA DE PAGINAÇÃO ---
$itens_por_pagina = 10; // Defina quantos usuários você quer por página
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) {
  $pagina_atual = 1;
}

// Conta o total de usuários para calcular o número de páginas
$total_itens = $usuarioModel->contarTotal();
$total_paginas = ceil($total_itens / $itens_por_pagina);

// Busca a lista de usuários de forma paginada
$lista = $usuarioModel->listarPaginadoComImobiliaria($pagina_atual, $itens_por_pagina);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Usuários Cadastrados</title>

  <!-- Bootstrap 5 CSS + FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />

  <!-- CSS global -->
  <link href="/assets/css/styles.css" rel="stylesheet" />
</head>

<body class="bg-light">

  <?php
  // Inclui o dashboard correto com base na permissão do usuário
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

      <!-- Bloco para exibir alertas -->
      <?php if (isset($_SESSION['sucesso'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($_SESSION['sucesso']);
          unset($_SESSION['sucesso']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
      <?php endif; ?>
      <?php if (isset($_SESSION['erro'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($_SESSION['erro']);
          unset($_SESSION['erro']); ?>
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
                <input type="text" id="searchInput" class="form-control"
                  placeholder="Pesquisar usuário..." onkeyup="filterTable()" />
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table id="usuarioTable" class="table table-hover table-striped align-middle">
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
                          <a href="editar.php?id=<?= $u['id_usuario'] ?>"
                            class="btn btn-outline-warning" title="Editar">
                            <i class="fas fa-pen"></i>
                          </a>
                          <a href="../../controllers/UsuarioController.php?excluir=<?= $u['id_usuario'] ?>"
                            class="btn btn-outline-danger" title="Excluir"
                            onclick="return confirm('Deseja realmente excluir este usuário?')">
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

          <!-- --- NOVO: CONTROLES DE PAGINAÇÃO --- -->
          <?php if ($total_paginas > 1): ?>
            <nav aria-label="Navegação da página de usuários">
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
  <script>
    function filterTable() {
      // Essa função de filtro JS só funcionará na página atual.
      // Uma busca completa no banco exigiria uma requisição AJAX.
      const input = document.getElementById('searchInput');
      const filter = input.value.toLowerCase();
      const rows = document.querySelectorAll('#usuarioTable tbody tr');

      rows.forEach(row => {
        const nomeCell = row.querySelector('td:nth-child(1)');
        const emailCell = row.querySelector('td:nth-child(2)');
        const nomeText = nomeCell.textContent.toLowerCase();
        const emailText = emailCell.textContent.toLowerCase();
        row.style.display =
          nomeText.includes(filter) || emailText.includes(filter) ? '' : 'none';
      });
    }
  </script>
</body>

</html>