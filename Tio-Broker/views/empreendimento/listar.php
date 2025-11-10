<?php
session_start();
require_once '../../config/config.php';

$activeMenu = 'listar empreendimento';
$titulo_pagina = 'Listar Empreendimento';
$conteudo = 'listar_empreendimento.php';

include '../layout/template_base.php';
