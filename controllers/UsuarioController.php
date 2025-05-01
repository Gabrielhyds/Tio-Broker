<?php
session_start();
require_once '../config/config.php';
require_once '../models/Usuario.php';

$usuario = new Usuario($connection);

function salvarFoto($inputName) {
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION);
        $novoNome = uniqid('foto_', true) . '.' . $ext;
        $caminho = '../uploads/' . $novoNome;

        if (!is_dir('../uploads')) mkdir('../uploads');
        move_uploaded_file($_FILES[$inputName]['tmp_name'], $caminho);

        return $caminho;
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'cadastrar') {
        $fotoPath = salvarFoto('foto');

        $usuario->cadastrar(
            $_POST['nome'],
            $_POST['email'],
            $_POST['cpf'],
            $_POST['telefone'],
            $_POST['senha'],
            $_POST['permissao'],
            $_POST['id_imobiliaria'],
            $_POST['creci'] ?? null,
            $fotoPath
        );
        header('Location: ../views/usuarios/listar.php?sucesso=1');
    }

    if ($_POST['action'] === 'atualizar') {
        $fotoPath = salvarFoto('foto');

        if (!$fotoPath) {
            $dadosAntigos = $usuario->buscarPorId($_POST['id_usuario']);
            $fotoPath = $dadosAntigos['foto'] ?? null;
        }

        $usuario->atualizar(
            $_POST['id_usuario'],
            $_POST['nome'],
            $_POST['email'],
            $_POST['cpf'],
            $_POST['telefone'],
            $_POST['permissao'],
            $_POST['id_imobiliaria'],
            $_POST['creci'] ?? null,
            $fotoPath
        );
        header('Location: ../views/usuarios/listar.php?atualizado=1');
    }
}

if (isset($_GET['excluir'])) {
    $usuario->excluir($_GET['excluir']);
    header('Location: ../views/usuarios/listar.php?excluido=1');
}
