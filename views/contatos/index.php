<?php

// Garante que uma sessão PHP seja iniciada se ainda não estiver ativa.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Definição de Caminho Raiz para Inclusões Robustas ---
// Define uma constante para o diretório raiz do projeto para evitar problemas com caminhos relativos.
// __DIR__ é '.../views/contatos', então subimos dois níveis para chegar em 'Tio-Broker/'.
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', realpath(__DIR__ . '/../../'));
}

// Inclui os arquivos de configuração usando o caminho absoluto.
require_once PROJECT_ROOT . '/config/config.php';
require_once PROJECT_ROOT . '/config/rotas.php';

// Verifica se a conexão com o banco de dados foi estabelecida em config.php.
if (!isset($connection)) {
    echo "<h1>Erro Crítico</h1><p>A variável de conexão com o banco de dados (\$connection) não foi definida no arquivo config.php.</p>";
    exit;
}

// Obtém o nome do controller e da action da URL.
$controllerName = $_GET['controller'] ?? 'cliente';
$action = $_GET['action'] ?? 'listar';

// Formata o nome da classe do Controller.
$controllerClass = ucfirst(strtolower($controllerName)) . 'Controller';

// Constrói o caminho completo e normalizado para o arquivo do Controller.
$controllerFile = PROJECT_ROOT . '/controllers/' . $controllerClass . '.php';

// Verifica se o arquivo do Controller existe.
if (file_exists($controllerFile)) {
    require_once $controllerFile;

    // Verifica se a classe do Controller existe após a inclusão.
    if (class_exists($controllerClass)) {
        try {
            // Instancia o Controller, passando a conexão com o banco de dados.
            $controllerInstance = new $controllerClass($connection);

            // Verifica se o método (a action) existe no controller.
            if (method_exists($controllerInstance, $action)) {
                // Executa a ação.
                $controllerInstance->$action();
            } else {
                echo "<h1>Erro de Roteamento</h1><p>A ação '{$action}' não foi encontrada no controller '{$controllerClass}'.</p>";
                exit;
            }
        } catch (Exception $e) {
            // Captura qualquer outra exceção durante a instanciação ou execução.
            echo "<h1>Erro Crítico na Execução</h1>";
            echo "<p>Ocorreu um erro ao tentar executar o controller '{$controllerClass}'.</p>";
            echo "<p><strong>Mensagem de Erro:</strong> " . $e->getMessage() . "</p>";
            exit;
        }
    } else {
        echo "<h1>Erro de Roteamento</h1><p>A classe '{$controllerClass}' não foi encontrada no arquivo '{$controllerFile}'. Verifique se o nome da classe está correto e se não há erros de sintaxe no arquivo.</p>";
        exit;
    }
} else {
    echo "<h1>Erro de Roteamento</h1><p>O arquivo do controller '{$controllerFile}' não foi encontrado.</p>";
    exit;
}
