<?php
require_once '../config/config.php';
session_start();

$id_tarefa = $_POST['id_tarefa'] ?? null;
$novo_status = $_POST['novo_status'] ?? null;

$permitidos = ['pendente', 'em andamento', 'concluida'];

if (!$id_tarefa || !in_array($novo_status, $permitidos)) {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    exit;
}

$stmt = $connection->prepare("UPDATE tarefas SET status = ? WHERE id_tarefa = ?");
$stmt->bind_param("si", $novo_status, $id_tarefa);
$ok = $stmt->execute();

echo json_encode(['success' => $ok]);
