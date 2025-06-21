<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../../config/config.php';
require_once '../../models/Imobiliaria.php';

$imobiliaria = new Imobiliaria($connection);
$listaImobiliarias = $imobiliaria->listarTodas();

$activeMenu = 'usuario_listar';
$conteudo = 'cadastrar_conteudo.php';
include '../layout/template_base.php';
