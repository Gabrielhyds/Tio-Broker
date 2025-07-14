<?php
// Inicia a sessão no topo, antes de qualquer coisa.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/config.php';
require_once '../../models/Imovel.php';

// 1. Proteção: Garante que o usuário está logado.
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

// 2. Pega os dados do usuário da sessão
$id_imobiliaria_usuario = $_SESSION['usuario']['id_imobiliaria'] ?? null;
$permissao = $_SESSION['usuario']['permissao'] ?? '';

// 3. Inicializa as variáveis que a View vai precisar
$imovelModel = new Imovel($connection);
$imoveis = [];
$imobiliarias = [];
$idSelecionada = null;

// 4. Lógica Centralizada para buscar os dados
try {
    if ($permissao === 'SuperAdmin') {
        // Se for SuperAdmin, busca a lista de todas as imobiliárias para o menu
        $stmt = $connection->query("SELECT id_imobiliaria, nome FROM imobiliaria ORDER BY nome");
        $imobiliarias = $stmt->fetch_all(MYSQLI_ASSOC);

        // Verifica se o SuperAdmin selecionou uma imobiliária no menu
        $idSelecionada = $_GET['id_imobiliaria'] ?? null;
        if ($idSelecionada) {
            // Se selecionou, busca os imóveis daquela imobiliária
            $imoveis = $imovelModel->buscarPorImobiliaria($idSelecionada);
        }
        // Se não selecionou, $imoveis continua como um array vazio.

    } else {
        // ✅ CORREÇÃO: Para as outras permissões, como Corretor, Admin, etc.,
        // busca apenas os imóveis da imobiliária associada ao usuário logado.
        if ($id_imobiliaria_usuario) {
            $imoveis = $imovelModel->buscarPorImobiliaria($id_imobiliaria_usuario);
        } else {
            // Caso de segurança: um usuário deste tipo deveria ter uma imobiliária.
            // Se não tiver, não mostra nada e pode opcionalmente registrar um erro.
            $imoveis = [];
        }
    }
} catch (Exception $e) {
    // Se ocorrer um erro no Model, exibe uma mensagem clara.
    die("ERRO FATAL NO BANCO DE DADOS: " . $e->getMessage());
}


// 5. Define as variáveis para o template base
$conteudo = 'listar_imovel_content.php'; // Arquivo de exibição
$titulo_pagina = 'Listar Imóveis';

// 6. Inclui o template base, que montará a página.
// Todas as variáveis definidas aqui ($imoveis, $imobiliarias, etc.)
// estarão disponíveis para o arquivo de conteúdo.
include '../layout/template_base.php';
