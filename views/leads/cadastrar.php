<?php
require_once '../../config/config.php';
require_once '../../models/Usuario.php';
require_once '../../models/Cliente.php';

// Define uma variável para identificar o menu ativo. Isso será usado na 'sidebar' para destacar o link correto.
$activeMenu = 'lead_cadastrar';
$conteudo = 'cadastrar_content.php';
include '../layout/template_base.php';