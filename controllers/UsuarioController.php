<?php
session_start();
require_once '../config/config.php';
require_once '../models/Usuario.php';

$usuario = new Usuario($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'cadastrar') {
        $usuario->cadastrar(
            $_POST['nome'],
            $_POST['email'],
            $_POST['cpf'],
            $_POST['telefone'],
            $_POST['senha'],
            $_POST['permissao'],
            $_POST['id_imobiliaria']
        );
        header('Location: ../views/usuarios/listar.php?sucesso=1');
    }

    if ($_POST['action'] === 'atualizar') {
        $usuario->atualizar(
            $_POST['id_usuario'],
            $_POST['nome'],
            $_POST['email'],
            $_POST['cpf'],
            $_POST['telefone'],
            $_POST['permissao'],
            $_POST['id_imobiliaria']
        );
        header('Location: ../views/usuarios/listar.php?atualizado=1');
    }
}

if (isset($_GET['excluir'])) {
    $usuario->excluir($_GET['excluir']);
    header('Location: ../views/usuarios/listar.php?excluido=1');
}
?>