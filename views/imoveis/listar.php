<?php
session_start();
require_once '../../config/config.php';
require_once '../../models/Imovel.php';

$imovelModel = new Imovel($connection);
$imoveis = $imovelModel->listarTodos();

$conteudo = 'listar_imovel_content.php';
$titulo_pagina = 'Listar Imóveis';

include '../layout/template_base.php';
