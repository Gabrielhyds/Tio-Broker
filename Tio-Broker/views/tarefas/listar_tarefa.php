<?php
session_start();
require_once '../../config/config.php';
require_once '../../models/Tarefa.php';

// Proteção: Garante que o usuário está logado.
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Variáveis do usuário logado
$id_usuario_logado = $_SESSION['usuario']['id_usuario'];
$id_imobiliaria_logada = $_SESSION['usuario']['id_imobiliaria'] ?? 0;
$permissao_usuario = $_SESSION['usuario']['permissao'] ?? '';

// --- Filtros opcionais via GET
$filtroUsuario = $_GET['usuario'] ?? '';
$filtroCliente = $_GET['cliente'] ?? '';

// Model de Tarefa
$tarefaModel = new Tarefa($connection);
$tarefas = $tarefaModel->listarPorPermissao(
    $id_usuario_logado,
    $id_imobiliaria_logada,
    $permissao_usuario,
    $filtroUsuario,
    $filtroCliente
);

// Busca usuários para o filtro
$sqlUsuarios = "SELECT id_usuario, nome FROM usuario";
$paramsUsuarios = [];
$typesUsuarios = "";

if ($permissao_usuario === 'Admin' || $permissao_usuario === 'Coordenador' || $permissao_usuario === 'Corretor') {
    $sqlUsuarios .= " WHERE id_imobiliaria = ?";
    $paramsUsuarios[] = $id_imobiliaria_logada;
    $typesUsuarios .= 'i';
}

$stmtUsuarios = $connection->prepare($sqlUsuarios);
if (!empty($paramsUsuarios)) {
    $stmtUsuarios->bind_param($typesUsuarios, ...$paramsUsuarios);
}
$stmtUsuarios->execute();
$resultUsuarios = $stmtUsuarios->get_result();
$usuarios = $resultUsuarios->fetch_all(MYSQLI_ASSOC);

// Busca clientes para o filtro
$sqlClientes = "SELECT id_cliente, nome FROM cliente";
$paramsClientes = [];
$typesClientes = "";

if ($permissao_usuario === 'Admin' || $permissao_usuario === 'Coordenador' || $permissao_usuario === 'Corretor') {
    $sqlClientes .= " WHERE id_imobiliaria = ?";
    $paramsClientes[] = $id_imobiliaria_logada;
    $typesClientes .= 'i';
}

$stmtClientes = $connection->prepare($sqlClientes);
if (!empty($paramsClientes)) {
    $stmtClientes->bind_param($typesClientes, ...$paramsClientes);
}
$stmtClientes->execute();
$resultClientes = $stmtClientes->get_result();
$clientes = $resultClientes->fetch_all(MYSQLI_ASSOC);

// Define variáveis para o template base
$activeMenu = 'tarefas';
$conteudo = 'listar_tarefa_content.php';
$titulo_pagina = 'Minhas Tarefas';

// Inclui o template base
include '../layout/template_base.php';
