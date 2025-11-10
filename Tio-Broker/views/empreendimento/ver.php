<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/config.php';
require_once '../../models/Empreendimento.php';

// --- LÓGICA DA PÁGINA ---

// Pega o ID da URL e valida
$id_empreendimento = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_empreendimento) {
    header('Location: listar_empreendimento.php');
    exit;
}

$empreendimentoModel = new Empreendimento($connection);
$empreendimento = $empreendimentoModel->buscarCompletoPorId($id_empreendimento);

// Se não encontrar o empreendimento, volta para a listagem
if (!$empreendimento) {
    $_SESSION['erro'] = "Empreendimento não encontrado.";
    header('Location: listar_empreendimento.php');
    exit;
}


// --- CONFIGURAÇÕES DO TEMPLATE ---

$activeMenu = 'ver empreendimento';
$titulo_pagina = 'Ver Empreendimento';
$conteudo = 'ver_empreendimento.php';

include '../layout/template_base.php';


