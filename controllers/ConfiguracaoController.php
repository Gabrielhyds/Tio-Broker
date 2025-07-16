<?php
// --- TESTE FINAL: ISOLAR O FICHEIRO DE CONFIGURAÇÃO ---
// Sabemos que o erro está num dos includes. Vamos testar apenas o config.php.

@session_start();
ob_start();

// Função de depuração final para capturar erros fatais.
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (headers_sent()) {
            return;
        }
        ob_clean();
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Ocorreu um erro fatal no servidor.',
            'error_details' => [
                'type'    => $error['type'],
                'message' => $error['message'],
                'file'    => $error['file'],
                'line'    => $error['line'],
            ]
        ]);
        exit;
    }
});

class ConfiguracaoController
{
    public function salvar()
    {
        $response = ['success' => false, 'message' => 'Ocorreu um erro desconhecido.'];
        $http_code = 500;

        try {
            // Incluímos apenas o config.php. O DAO está comentado.
            require_once __DIR__ . '/../config/config.php';
            // require_once __DIR__ . '/../model/DAO/ConfiguracaoDAO.php';

            // Verifica se o ficheiro de configuração realmente criou a variável de conexão.
            if (!isset($conexao) || !$conexao instanceof mysqli) {
                throw new Exception('A variável de conexão não foi definida corretamente no ficheiro config.php.');
            }

            // Verifica se o utilizador está logado.
            if (!isset($_SESSION['usuario']['id'])) {
                $http_code = 401; // Não Autorizado
                throw new Exception('Utilizador não autenticado. A sessão pode ter expirado.');
            }

            // Se chegarmos aqui, o config.php e a sessão estão a funcionar.
            $http_code = 200;
            $response = [
                'success' => true,
                'message' => 'Teste final bem-sucedido: config.php e a sessão foram carregados sem erros!'
            ];
        } catch (Throwable $e) {
            if ($http_code === 500) {
                $http_code = 500;
            }
            $response['message'] = $e->getMessage();
        }

        // Limpa qualquer saída que tenha sido bufferizada.
        ob_clean();

        // Envia a resposta final.
        http_response_code($http_code);
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// --- Roteamento Simples ---
if (isset($_GET['action']) && $_GET['action'] == 'salvar') {
    $controller = new ConfiguracaoController();
    $controller->salvar();
}
