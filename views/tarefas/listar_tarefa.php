<?php
session_start();
require_once '../../config/config.php';
require_once '../../models/Tarefa.php';

// Proteção: Garante que o usuário está logado.
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Cria uma instância do Model de Tarefa.
$tarefaModel = new Tarefa($connection);

// Pega os dados do usuário da sessão para usar no filtro.
$id_imobiliaria_logada = $_SESSION['usuario']['id_imobiliaria'] ?? 0;
$permissao_usuario = $_SESSION['usuario']['permissao'] ?? '';

// Chama o método listar com os argumentos necessários para o filtro de permissão.
$tarefas = $tarefaModel->listar($id_imobiliaria_logada, $permissao_usuario);

// Define as variáveis que o template base usará.
$activeMenu = 'tarefas';
$conteudo = 'listar_tarefa_content.php';
$titulo_pagina = 'Minhas Tarefas';

// Inclui o template base, que montará a página completa.
include '../layout/template_base.php';
