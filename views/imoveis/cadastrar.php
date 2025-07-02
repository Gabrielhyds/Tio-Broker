<?php
session_start();
require_once '../../config/config.php';

$activeMenu = 'imovel_cadastrar';
$titulo_pagina = 'Cadastrar Imóvel';
$conteudo = 'cadastrar_imovel.php';

include '../layout/template_base.php';
