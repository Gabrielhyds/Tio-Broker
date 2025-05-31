<?php
require_once '../../config/config.php';

$controllerName = $_GET['controller'] ?? 'cliente';
$action = $_GET['action'] ?? 'listar';

$controllerClass = ucfirst($controllerName) . 'Controller';
require_once "../../controllers/{$controllerClass}.php";

$controller = new $controllerClass($connection);

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    echo "Ação '$action' não encontrada no controller '$controllerClass'";
}
