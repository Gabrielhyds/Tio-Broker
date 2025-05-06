<?php

// Define o tipo de conteúdo da resposta como JSON
header('Content-Type: application/json');

// Verifica se o parâmetro 'cnpj' foi fornecido na URL (GET)
if (!isset($_GET['cnpj'])) {
    // Retorna erro 400 (Bad Request) e uma mensagem em JSON
    http_response_code(400);
    echo json_encode(['status' => 'ERROR', 'message' => 'CNPJ não fornecido']);
    exit;
}

// Remove todos os caracteres que não sejam números do CNPJ
$cnpj = preg_replace('/\D/', '', $_GET['cnpj']);

// Verifica se o CNPJ tem exatamente 14 dígitos
if (strlen($cnpj) !== 14) {
    // Retorna erro 400 (Bad Request) e mensagem de CNPJ inválido
    http_response_code(400);
    echo json_encode(['status' => 'ERROR', 'message' => 'CNPJ inválido']);
    exit;
}

// Monta a URL da API pública que será consultada
$url = "https://publica.cnpj.ws/cnpj/{$cnpj}";

// Inicializa o cURL com a URL da API
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Retorna o resultado como string
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // Desativa verificação do host SSL
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Desativa verificação do certificado SSL

// Executa a requisição
$response = curl_exec($curl);

// Encerra a sessão cURL
curl_close($curl);

// Verifica se a requisição falhou
if ($response === false) {
    http_response_code(500); // Erro interno do servidor
    echo json_encode(['status' => 'ERROR', 'message' => 'Erro ao consultar a API do CNPJ.ws']);
    exit;
}

// Converte a resposta JSON da API para array associativo
$data = json_decode($response, true);

// Verifica se a resposta contém a razão social
if (isset($data['razao_social'])) {
    // Retorna a razão social com status OK
    echo json_encode(['status' => 'OK', 'nome' => $data['razao_social']]);
} else {
    // Caso não contenha, retorna erro
    echo json_encode(['status' => 'ERROR', 'message' => 'CNPJ não encontrado']);
}

?>
