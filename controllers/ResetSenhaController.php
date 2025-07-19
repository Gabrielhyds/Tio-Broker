<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/PasswordResetModel.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../config/rotas.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Instancia os models
$passwordResetModel = new PasswordResetModel($connection);
$usuarioModel = new Usuario($connection);

// --- ROTA 1: USUÁRIO ENVIA O FORMULÁRIO PARA DEFINIR A NOVA SENHA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token']) && isset($_POST['senha'])) {
    $token = $_POST['token'];
    $novaSenha = $_POST['senha'];

    // Valida o token usando a lógica segura do model
    $dadosToken = $passwordResetModel->getToken($token);
    if (!$dadosToken) {
        // Se o token for inválido ou expirado, redireciona de volta com erro
        header("Location: " . BASE_URL . "views/auth/resetar_senha.php?token=$token&error=invalid_token");
        exit;
    }

    // Criptografa a nova senha usando md5 para manter a consistência com o sistema legado.
    $senhaCriptografada = md5($novaSenha);
    $id_usuario = $dadosToken['user_id'];

    // Tenta atualizar a senha no banco de dados
    if ($usuarioModel->atualizarSenha($id_usuario, $senhaCriptografada)) {
        // Invalida o token para que não possa ser usado novamente
        $passwordResetModel->invalidateToken($token);
        // Redireciona para o login com mensagem de sucesso
        header('Location: ' . BASE_URL . 'views/auth/login.php?reset=success');
        exit;
    } else {
        // Se houver um erro no banco, redireciona com erro genérico
        header("Location: " . BASE_URL . "views/auth/resetar_senha.php?token=$token&error=db_error");
        exit;
    }
}

// --- ROTA 2: USUÁRIO PEDE O LINK DE RECUPERAÇÃO DE SENHA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email_usuario = $_POST['email'];

    if ($email_usuario && filter_var($email_usuario, FILTER_VALIDATE_EMAIL)) {
        $usuario = $usuarioModel->buscarPorEmail($email_usuario);

        if ($usuario) {
            $token = bin2hex(random_bytes(32));
            $passwordResetModel->createToken($usuario['id_usuario'], $token);

            $mail = new PHPMailer(true);
            try {
                // --- CONFIGURAÇÃO PARA BREVO (SENDINBLUE) ---
                $mail->isSMTP();
                $mail->Host       = 'smtp-relay.brevo.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = '927f73001@smtp-brevo.com'; // <<< COLOQUE SEU E-MAIL DA BREVO
                $mail->Password   = 'J3QdCSBR6hxjfNWn';   // <<< COLOQUE SUA CHAVE SMTP
                $mail->Port       = 587;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->CharSet    = 'UTF-8';

                // O e-mail do remetente (setFrom) deve ser um e-mail verificado na sua conta Brevo.
                $mail->setFrom('tiodevs@gmail.com', 'Tio Broker');
                $mail->addAddress($email_usuario, $usuario['nome']);

                $link = BASE_URL . "views/auth/resetar_senha.php?token=$token";
                $logoUrl = 'https://i.imgur.com/your-logo-link.png'; // SUBSTITUA PELO LINK PÚBLICO DO SEU LOGO

                $mail->isHTML(true);
                $mail->Subject = 'Redefinição de Senha - TioBroker';
                $mail->Body    = <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Redefinição de Senha</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            font-family: 'Inter', Arial, sans-serif;
        }
        table {
            border-collapse: collapse;
        }
        .main-table {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #1d4ed8;
            padding: 30px 20px;
            text-align: center;
        }
        .content {
            padding: 40px 35px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        h1 {
            color: #111827;
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 15px 0;
        }
        p {
            color: #374151;
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 25px 0;
        }
        .button-container {
            padding: 10px 0 25px 0;
        }
        .button {
            background-color: #2563eb;
            color: #ffffff;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            display: inline-block;
        }
        .link {
            color: #2563eb;
            text-decoration: none;
        }
        .link-container {
            font-size: 14px;
            color: #4b5563;
            word-break: break-all;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <table class="main-table" align="center" cellpadding="0" cellspacing="0">
        <!-- Header -->
        <tr>
            <td class="header">
                <h3>TIO BROKER</h3>
            </td>
        </tr>
        <!-- Content -->
        <tr>
            <td class="content">
                <h1>Olá, {$usuario['nome']}!</h1>
                <p>Parabéns!!!😍 Recebemos uma solicitação para redefinir a senha da sua conta. Se foi taok, clique no botão abaixo para escolher uma nova senha.</p>
                <table class="button-container" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center">
                            <a href="{$link}" target="_blank" class="button">Redefinir Minha Senha</a>
                        </td>
                    </tr>
                </table>
                <p class="link-container">Se o botão não funcionar, copie e cole o seguinte link no seu navegador:<br><a href="{$link}" target="_blank" class="link">{$link}</a></p>
                <p style="font-size: 14px; color: #6b7280; margin-top: 30px; margin-bottom: 0;">Este link é válido por 1 hora. Se você não solicitou esta alteração, por favor se preocupe, você foi hackeado.</p>
            </td>
        </tr>
        <!-- Footer -->
        <tr>
            <td class="footer">
                &copy; Tio Broker. Todos os direitos reservados.<br>
                <span style="color: #9ca3af;">Você está a receber este e-mail porque uma redefinição de senha foi solicitada para a sua conta.</span>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

                $mail->send();
            } catch (Exception $e) {
                // error_log("Erro ao enviar e-mail de redefinição: {$mail->ErrorInfo}");
            }
        }
    }

    header("Location: " . BASE_URL . "views/auth/confirmacao_envio.php");
    exit;
}

header("Location: " . BASE_URL . "views/auth/login.php");
exit;
