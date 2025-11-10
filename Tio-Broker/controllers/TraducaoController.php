<?php

require_once '../models/Traducao.php';

header('Content-Type: application/json');

$traducaoModel = new Traducao();

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $modulo = $_GET['modulo'] ?? '';
    $idioma = $_GET['lang'] ?? 'pt-br';

    $dados = $traducaoModel->carregar($modulo, $idioma);

    if ($dados === null) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Tradução não encontrada']);
    } else {
        echo json_encode(['success' => true, 'data' => $dados]);
    }
} elseif ($metodo === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $modulo = $_GET['modulo'] ?? '';
    $idioma = $_GET['lang'] ?? 'pt-br';

    if (!$modulo || !$idioma || !is_array($input)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        exit;
    }

    $salvo = $traducaoModel->salvar($modulo, $idioma, $input);

    echo json_encode([
        'success' => $salvo,
        'message' => $salvo ? 'Tradução salva com sucesso' : 'Erro ao salvar'
    ]);
}
