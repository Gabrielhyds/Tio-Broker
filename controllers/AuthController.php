<?php

/*
|--------------------------------------------------------------------------
| ARQUIVO: controllers/AuthController.php (Versão Integrada e Corrigida)
|--------------------------------------------------------------------------
| Este é o seu AuthController completo, agora com a lógica para carregar
| as configurações do usuário no momento do login.
*/

// Inicia a sessão
session_start();

// Inclui os arquivos necessários
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/ConfiguracaoDAO.php'; // DAO de Configurações
require_once __DIR__ . '/../config/rotas.php';

// Verifica se a requisição é do tipo POST e a ação é "login"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Usa a conexão global do config.php
    global $connection;
    $usuario = new Usuario($connection);

    // Tenta autenticar o usuário
    $dados_usuario = $usuario->login($email, $senha);

    if ($dados_usuario) {
        // Verifica se o usuário está vinculado a uma imobiliária
        if (
            $dados_usuario['permissao'] !== 'SuperAdmin' &&
            (empty($dados_usuario['id_imobiliaria']) || $dados_usuario['id_imobiliaria'] == 0)
        ) {
            header('Location: ../views/auth/sem_imobiliaria.php');
            exit;
        }

        // --- NOVA LÓGICA DE CONFIGURAÇÕES ---
        // Instancia o DAO de configurações
        $configuracaoDAO = new ConfiguracaoDAO($connection);
        // Busca as configurações do usuário no banco
        $configuracoes = $configuracaoDAO->buscarConfiguracoes($dados_usuario['id_usuario']);

        // Se não encontrar configurações, define valores padrão
        if ($configuracoes === null) {
            $configuracoes = [
                'language' => 'pt-br',
                'appearance' => ['theme' => 'light'],
                'accessibility' => ['fontSize' => 'text-base', 'narrator' => false],
                'notifications' => ['sound' => true, 'visual' => true]
            ];
        }

        // Armazena os dados do usuário e suas configurações na sessão
        // **CORREÇÃO**: A chave agora é 'id_usuario' para consistência
        $_SESSION['usuario'] = [
            'id_usuario' => $dados_usuario['id_usuario'],
            'nome' => $dados_usuario['nome'],
            'email' => $dados_usuario['email'],
            'permissao' => $dados_usuario['permissao'],
            'foto' => $dados_usuario['foto'],
            'id_imobiliaria' => $dados_usuario['id_imobiliaria'],
            'configuracoes' => $configuracoes // Adiciona o array de configurações
        ];

        // Redireciona para o dashboard
        header('Location: ' . BASE_URL . 'views/dashboards/dashboard_unificado.php');
        exit;
    } else {
        // Se o login falhar, redireciona para a tela de erro
        header('Location: ../views/auth/login_invalido.php');
        exit;
    }
}
