<?php
session_start();

if (!isset($_SESSION['usuario'])) {
  header('Location: ../auth/login.php');
  exit;
}

$activeMenu = 'usuario_listar';

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Usuario.php';
require_once __DIR__ . '/../../models/Imobiliaria.php';

$usuarioModel = new Usuario($connection);
$imobiliariaModel = new Imobiliaria($connection);

$idUsuario = intval($_GET['id'] ?? 0);
$dados = $usuarioModel->buscarPorId($idUsuario);
if (!$dados) {
  header('Location: listar.php');
  exit;
}

$listaImobiliarias = $imobiliariaModel->listarTodas();
$salvoComSucesso = isset($_GET['salvo']) && $_GET['salvo'] == 1;

$conteudo = __DIR__ . '/editar_conteudo.php';
include '../layout/template_base.php';
