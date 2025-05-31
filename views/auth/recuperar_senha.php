<?php
// Tela para reset de senha - Solicitar link de recuperação
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha - Portal Imobiliário</title>
    <!-- Bootstrap CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #1379df 60%, #0d375f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .reset-box {
            background: #fff;
            padding: 2.5rem 2rem 2rem 2rem;
            border-radius: 1.2rem;
            box-shadow: 0 8px 40px rgba(19, 121, 223, 0.15), 0 1.5px 6px rgba(0,0,0,0.04);
            max-width: 410px;
            width: 100%;
            margin: auto;
        }
        .reset-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.2rem;
        }
        .reset-logo img {
            height: 54px;
            width: auto;
            margin-bottom: 8px;
        }
        .reset-logo span {
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
    <div class="reset-box shadow">
        <div class="reset-logo mb-3 flex-column">
            <img src="../assets/img/logo.png" alt="Logo Imobiliária">
        </div>
        <h4 class="mb-2 text-center" style="letter-spacing:.5px;color:#09304d;">Redefinir Senha</h4>
        <p class="mb-4 text-center text-muted small">
            Informe seu e-mail cadastrado e enviaremos um link para redefinir sua senha.
        </p>
        <!-- Formulário de recuperação de senha -->
        <form action="../../controllers/ResetSenhaController.php" method="POST" autocomplete="off">
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="seu@email.com" required>
                </div>
            </div>
            <div class="d-grid mt-4 mb-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane me-1"></i> Enviar link de redefinição
                </button>
            </div>
            <div class="mt-2 text-center">
                <a href="login.php" class="link-secondary small"><i class="fas fa-arrow-left me-1"></i>Voltar ao login</a>
            </div>
        </form>
    </div>
    <!-- Bootstrap JS + Font Awesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>
</html>
