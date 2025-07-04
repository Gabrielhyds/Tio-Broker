<?php
session_start();
require_once '../../config/config.php';
require_once '../../models/Imovel.php';

// Proteção: Garante que o usuário está logado.
if (!isset($_SESSION['usuario'])) {
    // Redireciona para a página de login se não houver sessão de usuário
    header('Location: ../auth/login.php');
    exit;
}

// Cria uma instância do Model de Imóvel
$imovelModel = new Imovel($connection);

// ** CORREÇÃO **: Pega os dados do usuário da sessão para usar no filtro.
$id_imobiliaria_logada = $_SESSION['usuario']['id_imobiliaria'] ?? 0;
$permissao_usuario = $_SESSION['usuario']['permissao'] ?? '';

// ** CORREÇÃO **: Chama o método listarTodos com os dois argumentos necessários.
$imoveis = $imovelModel->listarTodos($id_imobiliaria_logada, $permissao_usuario);

// Define as variáveis que o template base usará
$conteudo = 'listar_imovel_content.php'; // Arquivo que contém o HTML da lista
$titulo_pagina = 'Listar Imóveis'; // Título que aparecerá na aba do navegador

// Inclui o template base, que montará a página completa
include '../layout/template_base.php';
