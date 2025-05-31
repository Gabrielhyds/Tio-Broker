<?php
// Recebe token pela URL e mostra formulário para digitar nova senha
$token = $_GET['token'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Definir Nova Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f6fbff">
    <div class="container d-flex align-items-center justify-content-center vh-100">
        <div class="bg-white p-4 rounded shadow" style="max-width:370px;width:100%;">
            <h4 class="mb-3 text-center text-primary">Nova Senha</h4>
            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger">Token inválido ou expirado. Solicite novamente.</div>
            <?php endif; ?>
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success">Enviamos o link para seu e-mail!</div>
            <?php endif; ?>
            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger">E-mail não cadastrado.</div>
            <?php endif; ?>
            <form action="../../controllers/ResetSenhaController.php" method="POST">
                <input type="hidden" name="token" value="<?=$token?>">
                <div class="mb-3">
                    <label class="form-label">Nova senha</label>
                    <input type="password" name="senha" class="form-control" required>
                </div>
                <div class="d-grid mb-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Redefinir senha</button>
                </div>
            </form>
            <div class="text-center mt-2">
                <a href="login.php" class="link-secondary small">Voltar ao login</a>
            </div>
        </div>
    </div>
</body>
</html>
