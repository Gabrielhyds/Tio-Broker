<?php
session_start();
require_once '../../config/config.php';

$activeMenu = 'empreendimento_cadastrar';
$titulo_pagina = 'Cadastrar Empreendimento';
$conteudo = 'cadastrar_empreendimento.php';

include '../layout/template_base.php';
