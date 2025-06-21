<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['permissao'] !== 'SuperAdmin') {
  header('Location: ../auth/login.php');
  exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Imobiliaria.php';
require_once __DIR__ . '/../../models/Usuario.php';

$imobiliariaModel = new Imobiliaria($connection);
$usuarioModel     = new Usuario($connection);

$idImobiliaria = intval($_GET['id'] ?? 0);
$dadosImob     = $imobiliariaModel->buscarPorId($idImobiliaria);
if (!$dadosImob) {
  header('Location: listar.php');
  exit;
}

$usuarios = $usuarioModel->listarPorImobiliaria($idImobiliaria);

$stmt = $connection->prepare("
    SELECT id_usuario, nome
    FROM usuario
    WHERE id_imobiliaria IS NULL OR id_imobiliaria <> ?
    ORDER BY nome ASC
");
$stmt->bind_param("i", $idImobiliaria);
$stmt->execute();
$resultDisponiveis = $stmt->get_result();
$usuariosDisponiveis = $resultDisponiveis->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$usuarioRemovido = isset($_GET['removido']) && $_GET['removido'] == 1;
$usuarioIncluidoEscolhido = isset($_GET['incluidoUsuario']) && $_GET['incluidoUsuario'] == 1;

$activeMenu = 'imobiliaria_listar';
$conteudo = 'editar_imobiliaria_content.php';
include '../layout/template_base.php';
