<?php
session_start();
require_once '../../config/config.php';
require_once '../../models/Usuario.php';
require_once '../../models/Imobiliaria.php';

$id = $_GET['id'] ?? null;
$salvoComSucesso = $_GET['sucesso'] ?? null;

if (!$id) {
  $_SESSION['mensagem_erro'] = "Usuário não especificado.";
  header("Location: listar.php");
  exit;
}

$usuarioModel = new Usuario($connection);
$imobiliariaModel = new Imobiliaria($connection);

$usuario = $usuarioModel->buscarPorId($id);
if (!$usuario) {
  $_SESSION['mensagem_erro'] = "Usuário não encontrado.";
  header("Location: listar.php");
  exit;
}

$dados = $usuario;

$usuarioLogado = $_SESSION['usuario'];
$permissao = $usuarioLogado['permissao'];
$id_imobiliaria_usuario = $usuarioLogado['id_imobiliaria'];

if ($permissao === 'SuperAdmin') {
  $listaImobiliarias = $imobiliariaModel->listarTodas();
} else {
  $imob = $imobiliariaModel->buscarPorId($id_imobiliaria_usuario);
  $listaImobiliarias = $imob ? [$imob] : [];
}

$activeMenu = 'usuario_listar';
$conteudo = 'editar_conteudo.php';
include '../layout/template_base.php';
