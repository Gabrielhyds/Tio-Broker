<?php
require_once '../../config/config.php';
require_once '../../models/Usuario.php';
require_once '../../models/Cliente.php';
session_start();

$usuarioModel = new Usuario($connection);
$clienteModel = new Cliente($connection);

$usuarios = $usuarioModel->listarTodos();
$clientes = $clienteModel->listarTodos();

$activeMenu = 'tarefas';
$conteudo = 'cadastrar_tarefa_content.php';
include '../layout/template_base.php';
