<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/PasswordResetModel.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../config/rotas.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$passwordResetModel = new PasswordResetModel($connection);
$usuarioModel = new Usuario($connection);

// Se for envio de formulário de redefinição de senha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token']) && isset($_POST['senha'])) {
    $token = $_POST['token'];
    $novaSenha = $_POST['senha'];

    // Valida o token
    $dadosToken = $passwordResetModel->getToken($token);
    if (!$dadosToken) {
        header("Location: ../views/auth/resetar_senha.php?token=$token&error=1");
        exit;
    }

    $id_usuario = $dadosToken['user_id'];
    $senhaCriptografada = md5($novaSenha); // Dica: use password_hash() em produção

    // Atualiza a senha e remove o token
    if ($usuarioModel->atualizarSenha($id_usuario, $senhaCriptografada)) {
        $passwordResetModel->deleteToken($token);
        header('Location: ../views/auth/login.php?reset=success');
        exit;
    } else {
        header("Location: ../views/auth/resetar_senha.php?token=$token&error=1");
        exit;
    }
}

// Se for envio de e-mail para recuperação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email_usuario = $_POST['email'];

    if (!$email_usuario || !filter_var($email_usuario, FILTER_VALIDATE_EMAIL)) {
        die('E-mail inválido ou não informado.');
    }

    // Buscar usuário
    $usuario = (new Usuario($connection))->buscarPorEmail($email_usuario);
    if (!$usuario) {
        die("Usuário não encontrado.");
    }

    // Gera e salva o token no banco
    $token = bin2hex(random_bytes(32));
    $passwordResetModel->createToken($usuario['id_usuario'], $token);

    // Enviar e-mail
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = '54914b91210043';
        $mail->Password   = '71b60a904f79d8';
        $mail->Port       = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->setFrom('no-reply@tiobroker.com', 'Tio Broker');
        $mail->addAddress($email_usuario);

        $link = BASE_URL . "views/auth/resetar_senha.php?token=$token";

        $mail->isHTML(true);
        $mail->Subject = 'Redefinição de Senha - TioBroker';
        $mail->Body    = "Olá <strong>{$usuario['nome']}</strong>,<br><br>Clique no link abaixo para redefinir sua senha:<br><br><a href='$link'>$link</a><br><br>Este link é válido por 1 hora.";

        $mail->send();
        header("Location: ../views/auth/resetar_senha.php?success=1");
        exit;
    } catch (Exception $e) {
        echo "Erro ao enviar e-mail: {$mail->ErrorInfo}";
    }
}
