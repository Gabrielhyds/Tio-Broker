<?php
require_once '../config/config.php';
require_once '../models/Cliente.php';

header('Content-Type: application/json');

try {
    $clienteModel = new Cliente($connection);
    $clientes = $clienteModel->listarTodos(); // você deve implementar esse método no model
    echo json_encode(['success' => true, 'clientes' => $clientes]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar clientes.']);
}
