<?php
// Recebe o token de redefinição da URL. Usa o operador de coalescência nula para evitar erros se o token não estiver presente.
$token = $_GET['token'] ?? '';
?>
<!-- Declara o tipo de documento como HTML5. -->
<!DOCTYPE html>
<!-- O elemento raiz da página, com o idioma definido como português do Brasil. -->
<html lang="pt-br">

<head>
    <!-- Define o conjunto de caracteres como UTF-8. -->
    <meta charset="UTF-8">
    <!-- Define o título que aparecerá na aba do navegador. -->
    <title>Definir Nova Senha</title>
    <!-- Importa a folha de estilos do Bootstrap via CDN para estilização. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<!-- Define um fundo cinza claro para o corpo da página. -->

<body style="background:#f6fbff">
    <!-- Contêiner principal do Bootstrap para centralizar o conteúdo na tela. -->
    <div class="container d-flex align-items-center justify-content-center vh-100">
        <!-- A caixa (card) onde o formulário será exibido. -->
        <div class="bg-white p-4 rounded shadow" style="max-width:370px;width:100%;">
            <!-- Título da caixa. -->
            <h4 class="mb-3 text-center text-primary">Nova Senha</h4>
            <!-- Bloco PHP para exibir uma mensagem de erro se o token for inválido ou expirado. -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">Token inválido ou expirado. Solicite novamente.</div>
            <?php endif; ?>
            <!-- Bloco PHP para exibir uma mensagem de sucesso após o envio do link. (Esta mensagem geralmente estaria na página anterior, mas está aqui como exemplo). -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Enviamos o link para seu e-mail!</div>
            <?php endif; ?>
            <!-- Bloco PHP para exibir uma mensagem de erro se o e-mail não for encontrado. (Também da página anterior). -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">E-mail não cadastrado.</div>
            <?php endif; ?>
            <!-- Início do formulário para definir a nova senha. -->
            <form action="../../controllers/ResetSenhaController.php" method="POST">
                <!-- Campo oculto que envia o token recebido pela URL junto com o formulário. -->
                <input type="hidden" name="token" value="<?= $token ?>">
                <!-- Campo para a nova senha. -->
                <div class="mb-3">
                    <label class="form-label">Nova senha</label>
                    <input type="password" name="senha" class="form-control" required>
                </div>
                <!-- Botão para enviar o formulário e redefinir a senha. -->
                <div class="d-grid mb-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Redefinir senha</button>
                </div>
            </form>
            <!-- Link para voltar à página de login. -->
            <div class="text-center mt-2">
                <a href="login.php" class="link-secondary small">Voltar ao login</a>
            </div>
        </div>
    </div>
</body>

</html>