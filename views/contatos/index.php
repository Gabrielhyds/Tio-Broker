<?php
// index.php (localizado em algo como views/contatos/index.php)

// Garante que uma sessão PHP seja iniciada se ainda não estiver ativa.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Inclui o arquivo de configuração principal que deve definir a variável $connection.
// O caminho é relativo à localização deste arquivo index.php.
require_once __DIR__ . '/../../config/config.php'; // Garante que $connection está disponível

// 2. Obtém o nome do controller e da action da URL (usando o método GET).
// Usa o operador de coalescência nula (??) para definir valores padrão se não forem fornecidos.
$controllerName = $_GET['controller'] ?? 'cliente'; // Controller padrão: 'cliente'.
$action = $_GET['action'] ?? 'listar';           // Action padrão: 'listar'.

// 3. Formata o nome da classe do Controller.
// Ex: 'cliente' se torna 'ClienteController'. Isso padroniza a nomenclatura.
$controllerClass = ucfirst(strtolower($controllerName)) . 'Controller';

// 4. Constrói o caminho completo para o arquivo do Controller.
$controllerFile = __DIR__ . '/../../controllers/' . $controllerClass . '.php';

// 5. Verifica se o arquivo do Controller realmente existe no caminho especificado.
if (file_exists($controllerFile)) {
    // Se existir, inclui o arquivo para que a classe fique disponível.
    require_once $controllerFile;

    // 6. Verifica se a classe do Controller existe dentro do arquivo que foi incluído.
    if (class_exists($controllerClass)) {
        // 7. Tenta instanciar o Controller, passando a conexão com o banco de dados.
        // O bloco try...catch captura possíveis erros durante a criação do objeto.
        try {
            // Cria uma nova instância da classe do controller (ex: new ClienteController($connection)).
            $controllerInstance = new $controllerClass($connection);

            // 8. Verifica se o método (a action) existe dentro da classe do controller.
            if (method_exists($controllerInstance, $action)) {
                // 9. Se o método existir, ele é chamado. Esta é a etapa final do roteamento.
                $controllerInstance->$action();
            } else {
                // Se a action não for encontrada, define uma mensagem de erro na sessão.
                $_SESSION['mensagem_erro'] = "Ação '{$action}' não encontrada no controller '{$controllerClass}'.";
                // Registra um erro detalhado no log do servidor para depuração.
                error_log("Routing Error: Action '{$action}' not found in controller '{$controllerClass}'.");
                // Exibe uma mensagem de erro genérica para o usuário e interrompe a execução.
                echo "Erro: Ação não encontrada. Verifique os logs.";
                exit;
            }
            // Captura erros de tipo, como passar um argumento inválido para o construtor do controller.
        } catch (TypeError $e) {
            $_SESSION['mensagem_erro'] = "Erro ao instanciar o controller '{$controllerClass}'. Verifique o construtor.";
            error_log("Routing Error: Controller instantiation error for '{$controllerClass}': " . $e->getMessage());
            echo "Erro: Falha ao carregar o controller. Verifique os logs.";
            exit;
            // Captura outras exceções que possam ocorrer durante a instanciação.
        } catch (Exception $e) {
            $_SESSION['mensagem_erro'] = "Erro inesperado ao carregar o controller '{$controllerClass}'.";
            error_log("Routing Error: Unexpected error for '{$controllerClass}': " . $e->getMessage());
            echo "Erro: Ocorreu um problema. Verifique os logs.";
            exit;
        }
    } else {
        // Se a classe não for encontrada no arquivo, define e registra um erro.
        $_SESSION['mensagem_erro'] = "Controller '{$controllerClass}' não definido corretamente.";
        error_log("Routing Error: Class '{$controllerClass}' not found in file '{$controllerFile}'.");
        echo "Erro: Controller não definido. Verifique os logs.";
        exit;
    }
} else {
    // Se o arquivo do controller não for encontrado, define e registra um erro.
    $_SESSION['mensagem_erro'] = "Controller '{$controllerClass}' não encontrado.";
    error_log("Routing Error: Controller file '{$controllerFile}' not found.");
    echo "Erro: Controller não encontrado. Verifique os logs.";
    exit;
}
