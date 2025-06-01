<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f8f9fa;
    }

    .sidebar {
      height: 100vh;
      background-color: #343a40;
      color: #fff;
      position: fixed;
      top: 0;
      left: 0;
      width: 240px;
      padding-top: 1rem;
    }

    .sidebar a {
      color: #adb5bd;
      text-decoration: none;
      padding: 10px 20px;
      display: block;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: #495057;
      color: #fff;
    }

    .main {
      margin-left: 240px;
      padding: 20px;
    }

    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .card-icon {
      font-size: 2rem;
      color: #0d6efd;
    }

    @media (max-width: 768px) {
      .sidebar {
        position: relative;
        width: 100%;
        height: auto;
      }
      .main {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h4 class="text-center text-white">Coordenador</h4>
    <hr class="border-light" />
    <a href="#"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
    <a href="#"><i class="fas fa-tasks me-2"></i> Tarefas</a>
    <a href="#"><i class="fas fa-chart-bar me-2"></i> Relatórios</a>
    <a href="../chat/chat.php"><i class="fas fa-comments me-2"></i> Chat</a> <!-- Novo menu Chat -->
    <a href="../contatos/index.php?controller=cliente&action=listar"><i class="fas fa-address-book me-2"></i>Agenda de Contatos</a> <!-- Novo item -->
    <a href="#"><i class="fas fa-cogs me-2"></i> Configurações</a>
    <a href="../../controllers/LogoutController.php"><i class="fas fa-cogs me-2"></i>Sair</a>
   </div>

  <!-- Conteúdo Principal -->
  <div class="main">
    <!-- Topbar -->
    <div class="topbar mb-4">
      <h2>Dashboard</h2>
      <div class="d-flex align-items-center gap-3">
        <input class="form-control form-control-sm" type="search" placeholder="Buscar...">
        <img src="https://i.pravatar.cc/40" class="rounded-circle" alt="avatar" />
      </div>
    </div>


  



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
