<?php
// index.php (localizado em algo como views/contatos/index.php)

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Incluir o arquivo de configuração principal que estabelece $connection
// O caminho é relativo à localização deste arquivo index.php
// Se index.php está em Tio-Broker/views/contatos/, então ../../config/config.php está correto.
require_once __DIR__ . '/../../config/config.php'; // Garante que $connection está disponível

// 2. Obter o controller e a action da URL (com padrões)
$controllerName = $_GET['controller'] ?? 'cliente'; // Controller padrão: cliente
$action = $_GET['action'] ?? 'listar';             // Action padrão: listar

// 3. Formatar o nome da classe do Controller
// Ex: 'cliente' se torna 'ClienteController'
$controllerClass = ucfirst(strtolower($controllerName)) . 'Controller';

// 4. Construir o caminho para o arquivo do Controller
// Se index.php está em Tio-Broker/views/contatos/, então ../../controllers/ está correto.
$controllerFile = __DIR__ . '/../../controllers/' . $controllerClass . '.php';

// 5. Verificar se o arquivo do Controller existe
if (file_exists($controllerFile)) {
    require_once $controllerFile;

    // 6. Verificar se a classe do Controller existe
    if (class_exists($controllerClass)) {
        // 7. Instanciar o Controller, passando a conexão com o banco
        // Certifique-se que seus controllers (incluindo DocumentoController) 
        // aceitam $connection no construtor.
        try {
            $controllerInstance = new $controllerClass($connection); // $connection vem do config.php

            // 8. Verificar se o método (action) existe no controller
            if (method_exists($controllerInstance, $action)) {
                // 9. Chamar a action
                $controllerInstance->$action();
            } else {
                // Action não encontrada
                $_SESSION['mensagem_erro'] = "Ação '{$action}' não encontrada no controller '{$controllerClass}'.";
                error_log("Routing Error: Action '{$action}' not found in controller '{$controllerClass}'.");
                // Redirecionar para uma página de erro 404 ou dashboard
                // header('Location: index.php?controller=dashboard&action=index'); // Exemplo
                echo "Erro: Ação não encontrada. Verifique os logs."; // Mensagem simples
                exit;
            }
        } catch (TypeError $e) {
            // Captura erro se o construtor do controller não esperar $connection ou tiver assinatura errada
             $_SESSION['mensagem_erro'] = "Erro ao instanciar o controller '{$controllerClass}'. Verifique o construtor.";
            error_log("Routing Error: Controller instantiation error for '{$controllerClass}': " . $e->getMessage());
            echo "Erro: Falha ao carregar o controller. Verifique os logs.";
            exit;
        } catch (Exception $e) {
            // Outras exceções ao instanciar o controller
            $_SESSION['mensagem_erro'] = "Erro inesperado ao carregar o controller '{$controllerClass}'.";
            error_log("Routing Error: Unexpected error for '{$controllerClass}': " . $e->getMessage());
            echo "Erro: Ocorreu um problema. Verifique os logs.";
            exit;
        }
    } else {
        // Classe do Controller não encontrada no arquivo
        $_SESSION['mensagem_erro'] = "Controller '{$controllerClass}' não definido corretamente.";
        error_log("Routing Error: Class '{$controllerClass}' not found in file '{$controllerFile}'.");
        echo "Erro: Controller não definido. Verifique os logs.";
        exit;
    }
} else {
    // Arquivo do Controller não encontrado
    $_SESSION['mensagem_erro'] = "Controller '{$controllerClass}' não encontrado.";
    error_log("Routing Error: Controller file '{$controllerFile}' not found.");
    echo "Erro: Controller não encontrado. Verifique os logs.";
    exit;
}

?>
