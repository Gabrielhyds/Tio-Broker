<?php
session_start();
require_once '../config/config.php';
require_once '../models/Usuario.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['permissao'] !== 'SuperAdmin') {
    die("Acesso negado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'cadastrar') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $senha = $_POST['senha'];
    $permissao = $_POST['permissao'];

    $usuario = new Usuario($connection);
    if ($usuario->cadastrar($nome, $email, $cpf, $telefone, $senha, $permissao)) {
        header('Location: ../views/usuarios/cadastrar.php?sucesso=1');
    } else {
        echo "Erro ao cadastrar usuário.";
    }
}
?>
