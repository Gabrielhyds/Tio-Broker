<?php


header('Content-Type: application/json');

if (!isset($_GET['cnpj'])) {
    http_response_code(400);
    echo json_encode(['status' => 'ERROR', 'message' => 'CNPJ não fornecido']);
    exit;
}

$cnpj = preg_replace('/\D/', '', $_GET['cnpj']);

if (strlen($cnpj) !== 14) {
    http_response_code(400);
    echo json_encode(['status' => 'ERROR', 'message' => 'CNPJ inválido']);
    exit;
}

$url = "https://publica.cnpj.ws/cnpj/{$cnpj}";
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($curl);
curl_close($curl);

if ($response === false) {
    http_response_code(500);
    echo json_encode(['status' => 'ERROR', 'message' => 'Erro ao consultar a API do CNPJ.ws']);
    exit;
}

$data = json_decode($response, true);

if (isset($data['razao_social'])) {
    echo json_encode(['status' => 'OK', 'nome' => $data['razao_social']]);
} else {
    echo json_encode(['status' => 'ERROR', 'message' => 'CNPJ não encontrado']);
}


?>