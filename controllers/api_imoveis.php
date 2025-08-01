<?php
/**
 * API para buscar imóveis disponíveis para agendamento.
 * Conecta-se ao banco de dados para retornar uma lista real de imóveis,
 * filtrando pela imobiliária do usuário logado (exceto para SuperAdmin).
 */

// Define o cabeçalho da resposta como JSON.
header('Content-Type: application/json');

// Inicia a sessão se ainda não houver uma, para validação de segurança.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Importa a configuração do banco de dados.
require_once __DIR__ . '/../config/config.php';

// Função auxiliar para padronizar as respostas JSON e encerrar o script.
function responder_json_api($data, $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// ---- Validação de Segurança ----
// Garante que apenas usuários logados possam acessar esta API.
if (!isset($_SESSION['usuario']['id_usuario'])) {
    responder_json_api(['success' => false, 'message' => 'Acesso negado. Por favor, faça login novamente.'], 401);
}

// ---- Lógica de Busca no Banco de Dados com Filtro ----
try {
    // Pega os dados do usuário logado a partir da sessão.
    $id_imobiliaria_usuario = $_SESSION['usuario']['id_imobiliaria'] ?? null;
    $permissao_usuario = $_SESSION['usuario']['permissao'] ?? null;

    // Prepara a consulta SQL base para buscar imóveis relevantes para visita.
    $sql = "SELECT id_imovel, titulo FROM imovel WHERE status IN ('disponivel', 'reservado')";
    
    // Array para os parâmetros da consulta preparada.
    $params = [];
    $types = '';

    // APLICA A REGRA DE NEGÓCIO:
    // Se o usuário NÃO for 'SuperAdmin', adiciona o filtro por imobiliária.
    if ($permissao_usuario !== 'SuperAdmin') {
        // Se o usuário não for SuperAdmin e não tiver uma imobiliária vinculada,
        // ele não deve ver nenhum imóvel. Retornamos uma lista vazia.
        if (empty($id_imobiliaria_usuario)) {
            responder_json_api(['success' => true, 'imoveis' => []]);
        }
        
        // Adiciona a condição à consulta SQL e o parâmetro ao array.
        $sql .= " AND id_imobiliaria = ?";
        $params[] = $id_imobiliaria_usuario;
        $types .= 'i'; // 'i' para integer
    }

    $stmt = $connection->prepare($sql);
    
    if ($stmt === false) {
        throw new Exception('Falha ao preparar a consulta SQL: ' . $connection->error);
    }

    // Se houver parâmetros para vincular (caso não seja SuperAdmin), faz o bind.
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $imoveis = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Responde com sucesso e a lista de imóveis encontrada (filtrada ou não).
    responder_json_api(['success' => true, 'imoveis' => $imoveis]);

} catch (Exception $e) {
    // Em caso de erro, registra o problema e informa o usuário.
    error_log('Erro na API de imóveis: ' . $e->getMessage());
    responder_json_api(['success' => false, 'message' => 'Ocorreu um erro interno ao buscar os imóveis.'], 500);
}
