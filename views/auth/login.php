<?php
// Tela de login estilizada para portal imobiliário
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Portal Imobiliário</title>
    <!-- Bootstrap CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para ícones -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #1379df 60%, #0d375f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .login-box {
            background: #fff;
            padding: 2.5rem 2rem 2rem 2rem;
            border-radius: 1.2rem;
            box-shadow: 0 8px 40px rgba(19, 121, 223, 0.15), 0 1.5px 6px rgba(0,0,0,0.04);
            max-width: 410px;
            width: 100%;
            margin: auto;
            position: relative;
        }
        .login-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.2rem;
        }
        .login-logo i {
            font-size: 2.2rem;
            color: #1379df;
            margin-right: 0.5rem;
        }
        .login-logo span {
            font-size: 1.6rem;
            font-weight: bold;
            color: #0d375f;
        }
        .form-label {
            font-weight: 500;
            color: #0d375f;
        }
        .form-control {
            border-radius: 0.7rem;
            border: 1px solid #d5e2f6;
        }
        .form-control:focus {
            border-color: #1379df;
            box-shadow: 0 0 0 0.2rem rgba(19, 121, 223, .09);
        }
        .input-group-text {
            background: #eaf3fb;
            border: none;
            border-radius: 0.7rem 0 0 0.7rem;
            color: #1379df;
        }
        .btn-primary {
            background: linear-gradient(90deg,#1379df,#1b8bff 85%);
            border: none;
            border-radius: 0.7rem;
            font-weight: 500;
            box-shadow: 0 3px 12px rgba(19, 121, 223, 0.09);
            transition: 0.18s;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg,#0d375f,#1379df 90%);
        }
        .login-footer {
            margin-top: 2rem;
            text-align: center;
            font-size: .97rem;
            color: #8da1be;
        }
    </style>
</head>
<body>
    
    <div class="login-box shadow">
       <div class="login-logo mb-3 flex-column">
            <img src="../assets/img/logo.png" alt="Logo Imobiliária" style="height: 54px; width: auto; margin-bottom: 8px;">
        </div>
        <h3 class="mb-3 text-center" style="letter-spacing:.5px;color:#09304d;">Acesso ao Painel</h3>
        <?php if (isset($_GET['reset']) && $_GET['reset'] === 'success'): ?>
            <div class="alert alert-success">Senha redefinida com sucesso! Faça login com sua nova senha.</div>
            <?php endif; ?>

        <!-- Formulário de Login -->
        <form action="../../controllers/AuthController.php" method="POST" autocomplete="off">
            <input type="hidden" name="action" value="login">

            <!-- Campo de Email -->
            <div class="mb-3">
                <label for="email" class="form-label"  style="letter-spacing:.5px;color:#09304d;">E-mail</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="seu@email.com" required>
                </div>
            </div>

            <!-- Campo de Senha -->
            <div class="mb-3">
                <label for="senha" class="form-label"  style="letter-spacing:.5px;color:#09304d;">Senha</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="senha" class="form-control" placeholder="Sua senha" required>
                </div>
            </div>

            <!-- Botão de Login -->
            <div class="d-grid mb-2 mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt me-1"></i> Entrar
                </button>
            </div>
             <div class="mb-2 text-center">
                <a href="recuperar_senha.php" class="link-primary text-decoration-none small">
                    Esqueci minha senha?
                </a>
            </div>
        </form>
    </div>
    

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
