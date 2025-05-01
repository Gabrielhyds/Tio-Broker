<?php
session_start();
require_once '../config/config.php';
require_once '../models/Imobiliaria.php';

$imobiliaria = new Imobiliaria($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'cadastrar') {
        $nome = trim($_POST['nome']);
        $cnpj = trim($_POST['cnpj']);

        if ($imobiliaria->cadastrar($nome, $cnpj)) {
            header('Location: ../views/imobiliarias/listar.php?sucesso=1');
        } else {
            echo "Erro ao cadastrar imobiliária.";
        }
    }

    if ($_POST['action'] === 'atualizar') {
        $id = $_POST['id'];
        $nome = trim($_POST['nome']);
        $cnpj = trim($_POST['cnpj']);

        if ($imobiliaria->atualizar($id, $nome, $cnpj)) {
            header('Location: ../views/imobiliarias/listar.php?atualizado=1');
        } else {
            echo "Erro ao atualizar imobiliária.";
        }
    }
}

if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    if ($imobiliaria->excluir($id)) {
        header('Location: ../views/imobiliarias/listar.php?excluido=1');
    } else {
        echo "Erro ao excluir imobiliária.";
    }
}
?>