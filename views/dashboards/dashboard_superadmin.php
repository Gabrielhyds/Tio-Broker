<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Admin</title>

  <!-- Bootstrap 5 CSS + FontAwesome -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
    rel="stylesheet" 
  />
  <link 
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" 
    rel="stylesheet" 
  />

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f8f9fa;
      margin: 0;
      padding: 0;
    }

    /* Sidebar fixa à esquerda */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 240px;
      height: 100vh;
      background-color: #343a40;
      padding-top: 1rem;
      overflow-y: auto;
      color: #adb5bd;
    }
    .sidebar h4 {
      color: #fff;
      text-align: center;
      margin-bottom: 0.75rem;
    }
    .sidebar hr {
      border-color: #495057;
      margin: 0.5rem 0 1rem;
    }

    /* Nav principal */
    .sidebar .nav-link {
      color: #adb5bd;
      padding: 0.5rem 1rem;
      display: flex;
      align-items: center;
      font-size: 0.95rem;
    }
    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      background-color: #495057;
      color: #fff;
    }
    .sidebar .nav-link i {
      width: 1.25rem;
      text-align: center;
      margin-right: 0.5rem;
      color: #adb5bd;
    }
    .sidebar .nav-link.active i {
      color: #fff;
    }

    /* Subitens (indentados) */
    .sidebar .submenu-title {
      font-size: 0.85rem;
      text-transform: uppercase;
      padding: 0.5rem 1rem;
      color: #6c757d;
      margin-top: 1rem;
      letter-spacing: 0.03em;
    }
    .sidebar .submenu-item {
      padding-left: 2.5rem;
      font-size: 0.90rem;
    }
    .sidebar .submenu-item i {
      width: 1.1rem;
      text-align: center;
      margin-right: 0.4rem;
    }

    /* Espaço para o conteúdo principal */
    .main-content {
      margin-left: 240px;
      padding: 1.5rem;
    }

    /* Topbar dentro de main */
    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }
    .topbar .form-control {
      max-width: 200px;
    }

    /* Responsividade para telas pequenas */
    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
      }
      .main-content {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>

<!-- Sidebar SuperAdmin (usada via include) -->
<aside class="sidebar">
  <h4>SuperAdmin</h4>
  <hr />

  <!-- Submenu Imobiliária -->
  <span class="submenu-title">Imobiliária</span>
  <a href="../imobiliarias/cadastrar_imobiliaria.php" class="nav-link submenu-item<?= (basename($_SERVER['PHP_SELF']) == 'cadastrar_imobiliaria.php' ? ' active' : '') ?>">
    <i class="fas fa-plus"></i> Cadastrar
  </a>
  <a href="../imobiliarias/listar_imobiliaria.php" class="nav-link submenu-item<?= (basename($_SERVER['PHP_SELF']) == 'listar_imobiliaria.php' ? ' active' : '') ?>">
    <i class="fas fa-list"></i> Ver Imobiliárias
  </a>

  <!-- Submenu Usuário -->
  <span class="submenu-title">Usuário</span>
  <a href="../usuarios/cadastrar.php" class="nav-link submenu-item<?= (basename($_SERVER['PHP_SELF']) == 'cadastrar.php' ? ' active' : '') ?>">
    <i class="fas fa-user-plus"></i> Cadastrar Usuário
  </a>
  <a href="../usuarios/listar.php" class="nav-link submenu-item<?= (basename($_SERVER['PHP_SELF']) == 'listar.php' ? ' active' : '') ?>">
    <i class="fas fa-users-cog"></i> Gerenciar Usuários
  </a>

  <!-- Links fixos -->
  <span class="submenu-title mt-3">Ferramentas</span>
  <a href="../chat/chat.php" class="nav-link<?= (basename($_SERVER['PHP_SELF']) == 'chat.php' ? ' active' : '') ?>">
    <i class="fas fa-comments"></i> Chat
  </a>
  <a href="/configuracoes.php" class="nav-link<?= (basename($_SERVER['PHP_SELF']) == 'configuracoes.php' ? ' active' : '') ?>">
    <i class="fas fa-cogs"></i> Configurações
  </a>

  <hr />
  <a href="/controllers/LogoutController.php" class="nav-link mt-2">
    <i class="fas fa-sign-out-alt"></i> Sair
  </a>
</aside>
</aside>
  <!-- Bootstrap 5 JS Bundle (Popper + JS) -->
  <script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
  ></script>
</body>
</html>
