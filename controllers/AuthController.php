<?php
/**
 * Controlador de Autenticação (AuthController)
 *
 * Gerencia o processo de login (autenticação) e validações de acesso.
 * Este script é o principal ponto de entrada para iniciar a sessão do usuário.
 */

// Garante que a sessão seja iniciada antes de qualquer output
if (session_status() === PHP_SESSION_NONE) session_start();

// Dependências
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/ConfiguracaoDAO.php'; // DAO para configs de usuário
require_once __DIR__ . '/../config/rotas.php'; // Para a constante BASE_URL

// --- Processamento do Login ---
// O controlador só deve processar se for uma submissão de formulário (POST)
// e a ação específica for 'login'.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Utiliza a conexão global estabelecida no config.php
    global $connection;
    $usuario = new Usuario($connection);

    // Tenta autenticar e obter os dados do usuário
    $dados_usuario = $usuario->login($email, $senha);

    // 1. Login BEM-SUCEDIDO
    if ($dados_usuario) {

        // Regra de Negócio: Impede o login de usuários (não-SuperAdmin)
        // que não estejam associados a nenhuma imobiliária.
        if (
            $dados_usuario['permissao'] !== 'SuperAdmin' &&
            (empty($dados_usuario['id_imobiliaria']) || $dados_usuario['id_imobiliaria'] == 0)
        ) {
            // Redireciona para uma página de aviso específica
            header('Location: ../views/auth/sem_imobiliaria.php');
            exit;
        }

        // --- Carregamento das Configurações do Usuário ---
        // Busca as preferências de UI/UX (tema, idioma, etc.) do usuário no banco
        $configuracaoDAO = new ConfiguracaoDAO($connection);
        $configuracoes = $configuracaoDAO->buscarConfiguracoes($dados_usuario['id_usuario']);

        // Fallback: Se o usuário não tiver configurações salvas, define um conjunto padrão.
        // Isso evita erros em outras partes do sistema que esperam esses dados.
        if ($configuracoes === null) {
            $configuracoes = [
                'language' => 'pt-br',
                'appearance' => ['theme' => 'light'],
                'accessibility' => ['fontSize' => 'text-base', 'narrator' => false],
                'notifications' => ['sound' => true, 'visual' => true]
            ];
        }

        // Armazena os dados essenciais do usuário e suas configurações na sessão
        $_SESSION['usuario'] = [
            'id_usuario' => $dados_usuario['id_usuario'], // Identificador único
            'nome' => $dados_usuario['nome'],
            'email' => $dados_usuario['email'],
            'permissao' => $dados_usuario['permissao'], // Nível de acesso (ex: Admin, Corretor)
            'foto' => $dados_usuario['foto'],
            'id_imobiliaria' => $dados_usuario['id_imobiliaria'],
            'configuracoes' => $configuracoes // Preferências de UI/UX
        ];

        // Redireciona para o dashboard principal após o login
        header('Location: ' . BASE_URL . 'views/dashboards/dashboard_unificado.php');
        exit;

    // 2. Login FALHOU
    } else {
        // Redireciona de volta para uma página de erro de login
        header('Location: ../views/auth/login_invalido.php');
        exit;
    }
}

// 3. Acesso INDEVIDO (ex: GET ou POST sem a ação correta)
// Se o script foi acessado, mas não foi um POST de login, redireciona para o início.
// Isso impede o acesso direto ao arquivo pela URL.
header('Location: ' . BASE_URL . 'index.php');
exit;
