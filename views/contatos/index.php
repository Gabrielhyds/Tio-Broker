<?php

// Garante que uma sessão PHP seja iniciada se ainda não estiver ativa.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclui o arquivo de configuração principal que define a variável $connection.
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/rotas.php'; // Adicionado para garantir que a BASE_URL esteja sempre disponível

// Obtém o nome do controller e da action da URL.
$controllerName = $_GET['controller'] ?? 'cliente';
$action = $_GET['action'] ?? 'listar';

// Formata o nome da classe do Controller.
$controllerClass = ucfirst(strtolower($controllerName)) . 'Controller';

// Constrói o caminho completo para o arquivo do Controller.
$controllerFile = __DIR__ . '/../../controllers/' . $controllerClass . '.php';

// Verifica se o arquivo do Controller existe.
if (file_exists($controllerFile)) {
    require_once $controllerFile;

    // Verifica se a classe do Controller existe.
    if (class_exists($controllerClass)) {
        try {
            // Instancia o Controller, passando a conexão com o banco de dados.
            $controllerInstance = new $controllerClass($connection);

            // Verifica se o método (a action) existe no controller.
            if (method_exists($controllerInstance, $action)) {
                $controllerInstance->$action();
            } else {
                echo "Erro de Roteamento: A ação '{$action}' não foi encontrada no controller '{$controllerClass}'.";
                exit;
            }
            // CORREÇÃO: O bloco catch agora exibirá a mensagem de erro detalhada.
        } catch (Exception $e) {
            echo "<h1>Erro Crítico</h1>";
            echo "<p>Ocorreu um erro ao tentar carregar o controller '{$controllerClass}'.</p>";
            echo "<p><strong>Mensagem de Erro:</strong> " . $e->getMessage() . "</p>";
            echo "<p><strong>Arquivo:</strong> " . $e->getFile() . "</p>";
            echo "<p><strong>Linha:</strong> " . $e->getLine() . "</p>";
            echo "<pre>Stack Trace:\n" . $e->getTraceAsString() . "</pre>";
            exit;
        }
    } else {
        echo "Erro de Roteamento: A classe '{$controllerClass}' não foi encontrada no arquivo '{$controllerFile}'.";
        exit;
    }
} else {
    echo "Erro de Roteamento: O arquivo do controller '{$controllerFile}' não foi encontrado.";
    exit;
}
