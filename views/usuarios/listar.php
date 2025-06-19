<?php
session_start();

if (!isset($_SESSION['usuario'])) {
  header('Location: /auth/login.php');
  exit;
}

$activeMenu = 'usuario_listar';

require_once '../../config/config.php';
require_once '../../models/Usuario.php';

$usuarioModel = new Usuario($connection);

// --- LÓGICA DE FILTRO E PAGINAÇÃO ---
$itens_por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) {
  $pagina_atual = 1;
}
// Pega o termo de busca da URL
$filtro = $_GET['filtro'] ?? '';

// Conta o total de itens com base no filtro
$total_itens = $usuarioModel->contarTotal($filtro);
$total_paginas = ceil($total_itens / $itens_por_pagina);

// Busca os dados paginados com base no filtro
$lista = $usuarioModel->listarPaginadoComImobiliaria($pagina_atual, $itens_por_pagina, $filtro);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Usuários Cadastrados</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link href="/assets/css/styles.css" rel="stylesheet" />
</head>

<body class="bg-light">

  <?php
  // Bloco para incluir o dashboard correto
  if (isset($_SESSION['usuario']['permissao'])) {
    switch ($_SESSION['usuario']['permissao']) {
      case 'SuperAdmin':
        include_once '../dashboards/dashboard_superadmin.php';
        break;
      case 'Admin':
        include_once '../dashboards/dashboard_admin.php';
        break;
      case 'Coordenador':
        include_once '../dashboards/dashboard_coordenador.php';
        break;
      default:
        include_once '../dashboards/dashboard_corretor.php';
        break;
    }
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
          <form method="GET" action="listar.php" class="mb-4">
            <div class="row">
              <div class="col-md-5">
                <div class="input-group">
                  <input type="text" id="searchInput" name="filtro" class="form-control"
                    placeholder="Buscar por nome, email, permissão ou imobiliária..."
                    value="<?= htmlspecialchars($filtro) ?>">
                  <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                  </button>
                  <a href="listar.php" class="btn btn-outline-secondary" title="Limpar filtro">
                    <i class="fas fa-times"></i>
                  </a>
                </div>
              </div>
            </div>
          </form>

          <div class="table-responsive">
            <table id="usuarioTable" class="table table-hover table-striped align-middle">
              <thead>
                <tr>
                  <th>Nome</th>
                  <th>Email</th>
                  <th>Permissão</th>
                  <th>Imobiliária</th>
                  <th class="text-center">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($lista)): ?>
                  <?php foreach ($lista as $u): ?>
                    <tr>
                      <td><?= htmlspecialchars($u['nome']) ?></td>
                      <td><?= htmlspecialchars($u['email']) ?></td>
                      <td><?= htmlspecialchars($u['permissao']) ?></td>
                      <td><?= htmlspecialchars($u['nome_imobiliaria'] ?? '---') ?></td>
                      <td class="text-center">
                        <div class="btn-group btn-group-sm">
                          <a href="editar.php?id=<?= $u['id_usuario'] ?>"
                            class="btn btn-outline-warning" title="Editar"><i
                              class="fas fa-pen"></i></a>
                          <a href="../../controllers/UsuarioController.php?excluir=<?= $u['id_usuario'] ?>"
                            class="btn btn-outline-danger" title="Excluir"
                            onclick="return confirm('Deseja realmente excluir?')"><i
                              class="fas fa-trash"></i></a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center text-muted">Nenhum usuário encontrado.</td>
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