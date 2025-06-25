<?php

// Define que a resposta será no formato JSON
header('Content-Type: application/json');

/**
 * ATENÇÃO: Esta é uma API simulada (mock).
 * Ela retorna uma lista fixa de imóveis para permitir o desenvolvimento do frontend.
 * No futuro, esta lógica deve ser substituída por uma consulta real na tabela de imóveis
 * do banco de dados, retornando os imóveis associados à imobiliária do usuário logado.
 */

$imoveis_exemplo = [
    ['id_imovel' => 101, 'titulo_imovel' => 'Apto 2 Quartos - Centro'],
    ['id_imovel' => 102, 'titulo_imovel' => 'Casa com Piscina - Bairro Alto'],
    ['id_imovel' => 103, 'titulo_imovel' => 'Terreno Comercial - Av. Principal'],
    ['id_imovel' => 104, 'titulo_imovel' => 'Sobrado 3 Suítes - Condomínio Fechado'],
];

// Retorna os dados em formato JSON
echo json_encode(['success' => true, 'imoveis' => $imoveis_exemplo]);
