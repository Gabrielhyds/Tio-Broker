<?php
require_once '../../config/config.php';
require_once '../../models/Tarefa.php';
require_once '../../models/Usuario.php';
require_once '../../models/Cliente.php';
session_start();

$tarefaModel = new Tarefa($connection);
$usuarioModel = new Usuario($connection);
$clienteModel = new Cliente($connection);

$id = $_GET['id'] ?? null;
$tarefa = $tarefaModel->buscarPorId($id);
$usuarios = $usuarioModel->listarTodos();
$clientes = $clienteModel->listarTodos();

$activeMenu = 'tarefas';
$conteudo = 'editar_tarefa_content.php';
include '../layout/template_base.php';
