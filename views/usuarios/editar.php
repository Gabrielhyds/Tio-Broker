<?php
session_start();

// Verifica se o usuário está logado (você pode adaptar permissões aqui se desejar)
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

$activeMenu = 'usuario_listar';

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Usuario.php';
require_once __DIR__ . '/../../models/Imobiliaria.php';

$usuarioModel = new Usuario($connection);
$imobiliariaModel = new Imobiliaria($connection);

$idUsuario = intval($_GET['id'] ?? 0);
$dados = $usuarioModel->buscarPorId($idUsuario);
if (!$dados) {
    header('Location: listar.php');
    exit;
}

$listaImobiliarias = $imobiliariaModel->listarTodas();

// Feedbacks via GET (exemplo: ?salvo=1)
$salvoComSucesso = isset($_GET['salvo']) && $_GET['salvo'] == 1;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Editar Usuário</title>

  <!-- Bootstrap 5 CSS + FontAwesome -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    rel="stylesheet"
  />

  <!-- CSS global -->
  <link href="/assets/css/styles.css" rel="stylesheet" />

  <script>
    function apenasNumeros(event) {
      const tecla = event.key;
      if (!/\d/.test(tecla)) {
        event.preventDefault();
      }
    }

    function formatarCPF(campo) {
      let cpf = campo.value.replace(/\D/g, '');
      if (cpf.length > 11) cpf = cpf.slice(0, 11);
      cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
      cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
      cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
      campo.value = cpf;
    }

    function formatarTelefone(campo) {
      let tel = campo.value.replace(/\D/g, '');
      if (tel.length > 11) tel = tel.slice(0, 11);
      if (tel.length <= 10) {
        tel = tel.replace(/^(\d{2})(\d)/g, '($1) $2');
        tel = tel.replace(/(\d{4})(\d)/, '$1-$2');
      } else {
        tel = tel.replace(/^(\d{2})(\d)/g, '($1) $2');
        tel = tel.replace(/(\d{5})(\d)/, '$1-$2');
      }
      campo.value = tel;
    }

    function validarFormulario(event) {
      const cpfCampo = document.getElementById('cpf');
      const cpfLimpo = cpfCampo.value.replace(/\D/g, '');
      if (cpfLimpo.length !== 11) {
        event.preventDefault();
        alert('CPF inválido! O CPF deve conter 11 números.');
        cpfCampo.focus();
        return false;
      }
      // Poderia adicionar outras validações aqui se desejar
    }
  </script>
</head>
<body class="bg-light">

<?php
// Inclui o dashboard de acordo com a permissão do usuário logado
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
?>

<main class="main-content">
  <div class="container-fluid">

    <?php if ($salvoComSucesso): ?>
      <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        Usuário atualizado com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
      </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
      <h2 class="fw-bold"><i class="fas fa-pen me-2"></i>Editar Usuário</h2>
      <a href="listar.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Voltar à Lista
      </a>
    </div>

    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <form
          action="../../controllers/UsuarioController.php"
          method="POST"
          enctype="multipart/form-data"
          onsubmit="validarFormulario(event)"
        >
          <input type="hidden" name="action" value="atualizar" />
          <input type="hidden" name="id_usuario" value="<?= $dados['id_usuario'] ?>" />

          <div class="row g-3">
            <!-- Nome -->
            <div class="col-md-6">
              <label for="nome" class="form-label">Nome</label>
              <div class="input-group">
                <span class="input-group-text bg-white">
                  <i class="fas fa-user text-secondary"></i>
                </span>
                <input
                  type="text"
                  id="nome"
                  name="nome"
                  class="form-control"
                  value="<?= htmlspecialchars($dados['nome'], ENT_QUOTES) ?>"
                  required
                />
              </div>
            </div>

            <!-- Email -->
            <div class="col-md-6">
              <label for="email" class="form-label">Email</label>
              <div class="input-group">
                <span class="input-group-text bg-white">
                  <i class="fas fa-envelope text-secondary"></i>
                </span>
                <input
                  type="email"
                  id="email"
                  name="email"
                  class="form-control"
                  value="<?= htmlspecialchars($dados['email'], ENT_QUOTES) ?>"
                  required
                />
              </div>
            </div>

            <!-- CPF -->
            <div class="col-md-6">
              <label for="cpf" class="form-label">CPF</label>
              <div class="input-group">
                <span class="input-group-text bg-white">
                  <i class="fas fa-id-card text-secondary"></i>
                </span>
                <input
                  type="text"
                  id="cpf"
                  name="cpf"
                  class="form-control"
                  value="<?= htmlspecialchars($dados['cpf'], ENT_QUOTES) ?>"
                  maxlength="14"
                  required
                  onkeypress="apenasNumeros(event)"
                  oninput="formatarCPF(this)"
                />
              </div>
            </div>

            <!-- Telefone -->
            <div class="col-md-6">
              <label for="telefone" class="form-label">Telefone</label>
              <div class="input-group">
                <span class="input-group-text bg-white">
                  <i class="fas fa-phone text-secondary"></i>
                </span>
                <input
                  type="text"
                  id="telefone"
                  name="telefone"
                  class="form-control"
                  value="<?= htmlspecialchars($dados['telefone'], ENT_QUOTES) ?>"
                  maxlength="15"
                  required
                  onkeypress="apenasNumeros(event)"
                  oninput="formatarTelefone(this)"
                />
              </div>
            </div>

            <!-- CRECI -->
            <div class="col-md-6">
              <label for="creci" class="form-label">CRECI</label>
              <div class="input-group">
                <span class="input-group-text bg-white">
                  <i class="fas fa-id-badge text-secondary"></i>
                </span>
                <input
                  type="text"
                  id="creci"
                  name="creci"
                  class="form-control"
                  value="<?= htmlspecialchars($dados['creci'], ENT_QUOTES) ?>"
                />
              </div>
            </div>

            <!-- Foto Atual -->
            <div class="col-md-6">
              <label class="form-label">Foto atual</label><br />
              <?php if (!empty($dados['foto'])): ?>
                <img src="<?= htmlspecialchars($dados['foto'], ENT_QUOTES) ?>" alt="Foto do usuário" width="100" />
              <?php else: ?>
                <span class="text-muted">Nenhuma foto enviada.</span>
              <?php endif; ?>
            </div>

            <!-- Alterar Foto -->
            <div class="col-md-6">
              <label for="foto" class="form-label">Alterar Foto</label>
              <input type="file" id="foto" name="foto" class="form-control" />
            </div>

            <!-- Permissão -->
            <div class="col-md-6">
              <label for="permissao" class="form-label">Permissão</label>
              <select id="permissao" name="permissao" class="form-select" required>
                <option value="Admin" <?= $dados['permissao'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                <option value="Coordenador" <?= $dados['permissao'] === 'Coordenador' ? 'selected' : '' ?>>Coordenador</option>
                <option value="Corretor" <?= $dados['permissao'] === 'Corretor' ? 'selected' : '' ?>>Corretor</option>
              </select>
            </div>

            <!-- Imobiliária -->
            <div class="col-md-6">
              <label for="imobiliariaId" class="form-label">Imobiliária</label>
              <select id="imobiliariaId" name="imobiliariaId" class="form-select" required>
                <option value="">-- Selecione a Imobiliária --</option>
                <?php foreach ($listaImobiliarias as $imob): ?>
                  <option value="<?= $imob['id_imobiliaria'] ?>"
                    <?= $dados['id_imobiliaria'] == $imob['id_imobiliaria'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($imob['nome'], ENT_QUOTES) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

          </div>

          <div class="mt-4 d-flex justify-content-between">
            <a href="listar.php" class="btn btn-secondary">
              <i class="fas fa-arrow-left me-1"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save me-1"></i> Salvar Alterações
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>

<script
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>
</html>
