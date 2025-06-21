<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['permissao'] !== 'SuperAdmin') {
  header('Location: /auth/login.php');
  exit;
}

$activeMenu = 'imobiliaria_listar';

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Imobiliaria.php';

$imobiliariaModel = new Imobiliaria($connection);

// Lógica de paginação e filtro
$itens_por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$filtro = $_GET['filtro'] ?? '';

$total_itens = $imobiliariaModel->contarTotal($filtro);
$total_paginas = ceil($total_itens / $itens_por_pagina);
$lista = $imobiliariaModel->listarPaginado($pagina_atual, $itens_por_pagina, $filtro);

$activeMenu = 'imobiliaria_listar';
$conteudo = __DIR__ . '/listar_imobiliaria_content.php';
include '../layout/template_base.php';
