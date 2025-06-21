<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['usuario'])) {
  header('Location: /auth/login.php');
  exit;
}

$activeMenu = 'usuario_listar';

require_once '../../config/config.php';
require_once '../../models/Usuario.php';

$usuarioModel = new Usuario($connection);

// --- LÓGICA DE CONTROLE DE ACESSO ---
$usuarioLogado = $_SESSION['usuario'];
$permissao = $usuarioLogado['permissao'] ?? 'Admin';
$id_imobiliaria = $usuarioLogado['id_imobiliaria'] ?? null;

// --- LÓGICA DE FILTRO E PAGINAÇÃO ---
$itens_por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;

$filtro = $_GET['filtro'] ?? '';

// Aplica o filtro e restrição por permissão/imobiliária
$total_itens = $usuarioModel->contarTotal($filtro, $permissao, $id_imobiliaria);
$total_paginas = ceil($total_itens / $itens_por_pagina);

$lista = $usuarioModel->listarPaginadoComImobiliaria(
  $pagina_atual,
  $itens_por_pagina,
  $filtro,
  $permissao,
  $id_imobiliaria
);

// 🚀 Este é o arquivo que contém apenas o HTML
$conteudo = 'listar_content.php';
include_once '../layout/template_base.php';
