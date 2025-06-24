<?php

// Importa o arquivo de configuração do banco de dados
require_once '../config/config.php';

// Importa o modelo Cliente, que contém as funções de acesso ao banco
require_once '../models/Cliente.php';

// Define que o conteúdo retornado será no formato JSON
header('Content-Type: application/json');

try {
    // Cria uma instância do modelo Cliente, passando a conexão com o banco
    $clienteModel = new Cliente($connection);

    // Chama o método para listar todos os clientes (deve estar implementado no model)
    $clientes = $clienteModel->listarTodos();

    // Retorna os dados dos clientes em formato JSON, com sucesso = true
    echo json_encode(['success' => true, 'clientes' => $clientes]);
} catch (Exception $e) {
    // Em caso de erro, retorna uma mensagem de erro genérica em JSON
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar clientes.']);
}
