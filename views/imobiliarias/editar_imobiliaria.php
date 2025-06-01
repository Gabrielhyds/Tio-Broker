<?php
// views/imobiliarias/editar.php
session_start();

// Verifica se o usuário está logado e se tem permissão
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['permissao'] !== 'SuperAdmin') {
    header('Location: ../auth/login.php');
    exit;
}

// Define o menu ativo
$activeMenu = 'imobiliaria_listar';

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Imobiliaria.php';
require_once __DIR__ . '/../../models/Usuario.php';

$imobiliariaModel = new Imobiliaria($connection);
$usuarioModel     = new Usuario($connection);

// Busca dados da imobiliária pelo ID
$idImobiliaria = intval($_GET['id'] ?? 0);
$dadosImob     = $imobiliariaModel->buscarPorId($idImobiliaria);
if (!$dadosImob) {
    header('Location: listar_imobiliaria.php');
    exit;
}

// Busca todos os usuários vinculados a esta imobiliária
$usuarios = $usuarioModel->listarPorImobiliaria($idImobiliaria);

// Busca todos os usuários não vinculados a esta imobiliária
$stmt = $connection->prepare("
    SELECT id_usuario, nome
    FROM usuario
    WHERE id_imobiliaria IS NULL OR id_imobiliaria <> ?
    ORDER BY nome ASC
");
$stmt->bind_param("i", $idImobiliaria);
$stmt->execute();
$resultDisponiveis = $stmt->get_result();
$usuariosDisponiveis = $resultDisponiveis->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Checa se houve remoção de usuário para exibir feedback
$usuarioRemovido = isset($_GET['removido']) && $_GET['removido'] == 1;

// Checa se houve inclusão de usuário para exibir feedback
$usuarioIncluidoEscolhido = isset($_GET['incluidoUsuario']) && $_GET['incluidoUsuario'] == 1;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Imobiliária</title>

  <!-- Bootstrap 5 CSS + FontAwesome -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    rel="stylesheet"
  >

  <!-- CSS global (contém estilos do sidebar) -->
  <link href="/assets/css/styles.css" rel="stylesheet">

  <script>
    function apenasNumeros(event) {
      const tecla = event.key;
      if (!/\d/.test(tecla)) {
        event.preventDefault();
      }
    }

    function formatarCNPJ(campo) {
      let cnpj = campo.value.replace(/\D/g, '');
      if (cnpj.length > 14) cnpj = cnpj.slice(0, 14);
      cnpj = cnpj.replace(/^(\d{2})(\d)/, "$1.$2");
      cnpj = cnpj.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
      cnpj = cnpj.replace(/\.(\d{3})(\d)/, ".$1/$2");
      cnpj = cnpj.replace(/(\d{4})(\d)/, "$1-$2");
      campo.value = cnpj;
    }

    function validarFormulario(event) {
      const cnpjCampo = document.getElementById('cnpj');
      const cnpjLimpo = cnpjCampo.value.replace(/\D/g, '');
      if (cnpjLimpo.length !== 14) {
        event.preventDefault();
        alert('CNPJ inválido! O CNPJ deve conter 14 números.');
        cnpjCampo.focus();
      }
    }
  </script>
</head>
<body class="bg-light">

<?php
// Inclui o dashboard de acordo com a permissão
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

      <!-- ALERTAS DE FEEDBACK -->
      <?php if ($usuarioRemovido): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
          <i class="fas fa-check-circle me-2"></i>
          Usuário removido da imobiliária com sucesso!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
      <?php endif; ?>

      <?php if ($usuarioIncluidoEscolhido): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
          <i class="fas fa-check-circle me-2"></i>
          Usuário vinculado à imobiliária com sucesso!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
      <?php endif; ?>

      <!-- Título e botão “Voltar à Lista” -->
      <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
        <h2 class="fw-bold"><i class="fas fa-pen me-2"></i>Editar Imobiliária</h2>
        <a href="listar.php" class="btn btn-outline-secondary">
          <i class="fas fa-arrow-left me-1"></i> Voltar à Lista
        </a>
      </div>

      <!-- Formulário em card para editar imobiliária -->
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <form
            action="../../controllers/ImobiliariaController.php"
            method="POST"
            onsubmit="validarFormulario(event)"
          >
            <input type="hidden" name="action" value="atualizar">
            <input type="hidden" name="id" value="<?= $dadosImob['id_imobiliaria'] ?>">

            <div class="row g-3">
              <!-- Nome da Imobiliária -->
              <div class="col-md-6">
                <label for="nome" class="form-label">Nome da Imobiliária</label>
                <div class="input-group">
                  <span class="input-group-text bg-white">
                    <i class="fas fa-building text-secondary"></i>
                  </span>
                  <input
                    type="text"
                    id="nome"
                    name="nome"
                    class="form-control"
                    value="<?= htmlspecialchars($dadosImob['nome'], ENT_QUOTES) ?>"
                    placeholder="Ex: Imobiliária Exemplo Ltda."
                    required
                  >
                </div>
              </div>

              <!-- CNPJ -->
              <div class="col-md-6">
                <label for="cnpj" class="form-label">CNPJ</label>
                <div class="input-group">
                  <span class="input-group-text bg-white">
                    <i class="fas fa-id-card text-secondary"></i>
                  </span>
                  <input
                    type="text"
                    id="cnpj"
                    name="cnpj"
                    class="form-control"
                    value="<?= htmlspecialchars($dadosImob['cnpj'], ENT_QUOTES) ?>"
                    placeholder="00.000.000/0000-00"
                    maxlength="18"
                    required
                    onkeypress="apenasNumeros(event)"
                    oninput="formatarCNPJ(this)"
                  >
                </div>
              </div>
            </div>

            <!-- Botões de ação -->
            <div class="mt-4 d-flex justify-content-end gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Salvar Alterações
              </button>
              <a href="listar.php" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i> Cancelar
              </a>
            </div>
          </form>
        </div>
      </div>

      <!-- Card para adicionar novo usuário -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
          <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Adicionar Usuário</h5>
        </div>
        <div class="card-body">
          <?php if (!empty($usuariosDisponiveis)): ?>
            <form
              action="../../controllers/UsuarioController.php"
              method="GET"
              class="row g-3 align-items-end"
            >
              <input type="hidden" name="idImobiliaria" value="<?= $idImobiliaria ?>">

              <div class="col-md-8">
                <label for="selectUsuario" class="form-label">Selecione Usuário</label>
                <select id="selectUsuario" name="incluirUsuario" class="form-select" required>
                  <option value="" disabled selected>Escolha um usuário...</option>
                  <?php foreach ($usuariosDisponiveis as $disp): ?>
                    <option value="<?= $disp['id_usuario'] ?>">
                      <?= htmlspecialchars($disp['nome'], ENT_QUOTES) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-4">
                <button type="submit" class="btn btn-success w-100">
                  <i class="fas fa-plus me-1"></i> Adicionar
                </button>
              </div>
            </form>
          <?php else: ?>
            <p class="text-center text-muted mb-0">
              Não há usuários disponíveis para vincular.
            </p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Card com lista de usuários vinculados -->
      <div class="card shadow-sm">
        <div class="card-header bg-white">
          <h5 class="mb-0"><i class="fas fa-users me-2"></i>Usuários da Imobiliária</h5>
        </div>
        <div class="card-body">
          <?php if (!empty($usuarios)): ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Email</th>
                    <th scope="col" class="text-center">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($usuarios as $user): ?>
                    <tr>
                      <td><?= $user['id_usuario'] ?></td>
                      <td><?= htmlspecialchars($user['nome'], ENT_QUOTES) ?></td>
                      <td><?= htmlspecialchars($user['email'], ENT_QUOTES) ?></td>
                      <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group">
                          <!-- Editar Usuário -->
                          <a
                            href="../usuarios/editar.php?id=<?= $user['id_usuario'] ?>"
                            class="btn btn-outline-warning"
                            title="Editar Usuário"
                          >
                            <i class="fas fa-pen"></i>
                          </a>
                          <!-- Remover vínculo com esta imobiliária -->
                          <a
                            href="../../controllers/UsuarioController.php?removerImobiliaria=<?= $user['id_usuario'] ?>&idImobiliaria=<?= $idImobiliaria ?>"
                            class="btn btn-outline-danger"
                            title="Remover da Imobiliária"
                            onclick="return confirm('Deseja realmente remover este usuário da imobiliária?')"
                          >
                            <i class="fas fa-user-minus"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="text-center text-muted mb-0">Nenhum usuário vinculado a esta imobiliária.</p>
          <?php endif; ?>
        </div>
      </div>
      <!-- Fim do card de usuários -->

    </div>
  </main>

  <!-- Bootstrap 5 JS Bundle (Popper + JS) -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
  ></script>
</body>
</html>
