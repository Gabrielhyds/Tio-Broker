<?php

// Inicia a sessão (necessária para salvar os dados do usuário após o login)
session_start();

// Inclui o arquivo de configuração do banco de dados
require_once __DIR__ . '/../config/config.php';

// Inclui o modelo de usuário, responsável por autenticação e dados relacionados
require_once __DIR__ . '/../models/Usuario.php';

// Inclui o arquivo de rotas para usar a constante BASE_URL
require_once __DIR__ . '/../config/rotas.php';

// Verifica se a requisição é do tipo POST e se a ação é "login"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {

    // Captura os dados enviados pelo formulário
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Instancia o objeto Usuario com a conexão ao banco
    $usuario = new Usuario($connection);

    // Tenta autenticar o usuário com o e-mail e senha informados
    $dados = $usuario->login($email, $senha);

    // Se encontrou um usuário correspondente
    if ($dados) {

        // Verifica se o usuário está vinculado a uma imobiliária (exceto se for SuperAdmin)
        if (
            $dados['permissao'] !== 'SuperAdmin' &&
            (
                !isset($dados['id_imobiliaria']) ||
                $dados['id_imobiliaria'] === '' ||
                $dados['id_imobiliaria'] === null ||
                $dados['id_imobiliaria'] == 0
            )
        ) {
            // Redireciona para a tela de erro se não estiver vinculado a uma imobiliária
            header('Location: ../views/auth/sem_imobiliaria.php');
            exit;
        }

        // Login válido: armazena os dados do usuário na sessão
        $_SESSION['usuario'] = $dados;

        // Redireciona para o dashboard unificado
        header('Location: ' . BASE_URL . 'views/dashboards/dashboard_unificado.php');
        exit;
    } else {
        // Se o login falhar, redireciona para a tela de login inválido
        header('Location: ../views/auth/login_invalido.php');
        exit;
    }
}
