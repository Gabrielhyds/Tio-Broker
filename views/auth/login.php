<?php
// Tela de login
//Formulários HTML para o usuário preencher.
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <!-- Bootstrap CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilo adicional para o fundo */
        body {
            background-color:rgb(19, 121, 223);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <!-- Container do formulário -->
    <div class="bg-white p-5 rounded shadow" style="width: 100%; max-width: 400px;">
        <h2 class="mb-4 text-center">Login</h2>
        <!-- Formulário de Login -->
        <form action="../../controllers/AuthController.php" method="POST">
            <!-- Campo oculto para indicar a ação -->
            <input type="hidden" name="action" value="login">
            
            <!-- Campo de Email -->
            <div class="mb-3">
                <label for="email" class="form-label">E-mail:</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <!-- Campo de Senha -->
            <div class="mb-3">
                <label for="senha" class="form-label">Senha:</label>
                <input type="password" name="senha" class="form-control" required>
            </div>

            <!-- Botão de Login -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Entrar</button>
            </div>

        </form>
    </div>

    <!-- Bootstrap JS via CDN (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


