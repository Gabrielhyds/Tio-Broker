<?php
session_start();
require_once '../../config/config.php';
require_once '../../models/Usuario.php';
require_once '../../models/Imobiliaria.php';

$id = $_GET['id'] ?? null;
$salvoComSucesso = $_GET['sucesso'] ?? null;

$usuarioModel = new Usuario($connection);
$imobiliariaModel = new Imobiliaria($connection);

$usuario = $usuarioModel->buscarPorId($id);
$dados = $usuario; // se o conteúdo usa $dados

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
