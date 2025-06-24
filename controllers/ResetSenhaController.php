<?php
// Inclui o arquivo de configuração principal, que estabelece a conexão com o banco de dados.
require_once __DIR__ . '/../config/config.php';
// Inclui o Model do Usuário, que contém métodos para interagir com a tabela de usuários.
require_once __DIR__ . '/../models/UserModel.php';
// Inclui o Model de Redefinição de Senha, para gerenciar os tokens de reset.
require_once __DIR__ . '/../models/PasswordResetModel.php';

// Cria uma nova instância do UserModel, passando a variável de conexão ($connection).
$userModel = new UserModel($connection);
// Cria uma nova instância do PasswordResetModel, passando a conexão.
$resetModel = new PasswordResetModel($connection);

// Bloco para lidar com o envio do formulário de solicitação de recuperação de senha.
// Verifica se a requisição é do tipo POST e se o campo 'email' foi enviado.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    // Remove espaços em branco do início и do fim do email fornecido.
    $email = trim($_POST['email']);
    // Busca um usuário no banco de dados com o email fornecido.
    $user = $userModel->findByEmail($email);

    // Se um usuário com o email correspondente for encontrado.
    if ($user) {
        // Gera um token criptograficamente seguro e o converte para hexadecimal.
        $token = bin2hex(random_bytes(32));
        // Salva o token no banco de dados, associado ao ID do usuário.
        $resetModel->createToken($user['id_usuario'], $token);

        // Monta o link completo para a página de redefinição de senha, incluindo o token.
        $resetLink = "http://localhost/tio-broker/app/views/resetar_senha.php?token=$token";

        // --- PREPARAÇÃO PARA O ENVIO DE EMAIL ---
        // Define o assunto do email.
        $assunto = "Redefinição de Senha - Portal Imobiliário";
        // Monta o corpo da mensagem do email.
        $mensagem = "Olá, clique no link abaixo para redefinir sua senha:\n$resetLink\nEste link é válido por 1 hora.";
        // Define os cabeçalhos do email, incluindo o remetente.
        $headers = "From: no-reply@seudominio.com\r\n";

        // Tenta enviar o email para o usuário. (NOTA: requer configuração de servidor de email para funcionar).
        mail($email, $assunto, $mensagem, $headers);
        // Redireciona para a página de solicitação com um parâmetro de sucesso.
        header("Location: ../views/auth/resetar_senha.php?success=1");
        // Encerra a execução do script.
        exit;
    } else {
        // Se o email não for encontrado, redireciona de volta com um parâmetro de erro.
        header("Location: ../views/auth/resetar_senha.php?error=1");
        // Encerra a execução do script.
        exit;
    }
}

// Bloco para processar a atualização da senha a partir da página de nova senha.
// Verifica se a requisição é POST e se contém o token e a nova senha.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token']) && isset($_POST['senha'])) {
    // Pega o token enviado pelo formulário.
    $token = $_POST['token'];
    // Pega a nova senha enviada pelo formulário.
    $novaSenha = $_POST['senha'];

    // Busca os dados do token no banco de dados para validá-lo.
    $tokenData = $resetModel->getToken($token);
    // Se o token for válido e encontrado.
    if ($tokenData) {
        // Gera um hash seguro da nova senha.
        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);

        // Atualiza a senha do usuário no banco de dados usando o ID associado ao token.
        $userModel->updatePassword($tokenData['user_id'], $hash);

        // Após o uso, remove o token do banco de dados para que não possa ser usado novamente.
        $resetModel->deleteToken($token);

        // Redireciona para a página de login com uma mensagem de sucesso na redefinição.
        header("Location: ../views/auth/login.php?reset=success");
        // Encerra a execução do script.
        exit;
    } else {
        // Se o token for inválido ou expirado, redireciona de volta para a página de reset com um erro.
        header("Location: ../views/auth/resetar_senha.php?token=$token&error=invalid");
        // Encerra a execução do script.
        exit;
    }
}
