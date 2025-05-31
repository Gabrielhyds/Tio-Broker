<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PasswordResetModel.php';

// Agora sim, $conn está definido antes de instanciar os models!
$userModel = new UserModel($connection);
$resetModel = new PasswordResetModel($connection);
// Envio do formulário de recuperação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $user = $userModel->findByEmail($email);

    if ($user) {
        // Gera token único
        $token = bin2hex(random_bytes(32));
        $resetModel->createToken($user['id_usuario'], $token);

        // Monta link de reset
       $resetLink = "http://localhost/tio-broker/app/views/resetar_senha.php?token=$token";

        // ENVIO DE EMAIL (ajuste conforme seu servidor)
        $assunto = "Redefinição de Senha - Portal Imobiliário";
        $mensagem = "Olá, clique no link abaixo para redefinir sua senha:\n$resetLink\nEste link é válido por 1 hora.";
        $headers = "From: no-reply@seudominio.com\r\n";

        mail($email, $assunto, $mensagem, $headers);
        header("Location: ../views/auth/resetar_senha.php?success=1"); // Mensagem de sucesso
        exit;
    } else {
        header("Location: ../views/auth/resetar_senha.php?error=1"); // Email não encontrado
        exit;
    }
}

// Atualiza a senha do usuário (página de nova senha)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token']) && isset($_POST['senha'])) {
    $token = $_POST['token'];
    $novaSenha = $_POST['senha'];

    $tokenData = $resetModel->getToken($token);
    if ($tokenData) {
        // Hash da nova senha
        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);

        // Atualiza senha
        $userModel->updatePassword($tokenData['user_id'], $hash);

        // Remove token usado
        $resetModel->deleteToken($token);

        header("Location: ../views/auth/login.php?reset=success");
        exit;
    } else {
        header("Location: ../views/auth/resetar_senha.php?token=$token&error=invalid");
        exit;
    }
}
?>
