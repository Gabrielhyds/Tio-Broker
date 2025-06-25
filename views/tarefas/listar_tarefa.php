<?php
require_once '../../config/config.php';
require_once '../../models/Tarefa.php';
session_start();

$tarefaModel = new Tarefa($connection);
$tarefas = $tarefaModel->listar();

$activeMenu = 'tarefas';
$conteudo = 'listar_tarefa_content.php';
include '../layout/template_base.php';
