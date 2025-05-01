<?php
require_once '../../config/config.php';
require_once '../../models/Usuario.php';

$usuario = new Usuario($connection);
$lista = $usuario->listarTodosComImobiliaria();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Usuários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Usuários Cadastrados</h2>
        <?php if (isset($_GET['sucesso'])): ?>
            <div class="alert alert-success">Usuário cadastrado com sucesso!</div>
        <?php elseif (isset($_GET['atualizado'])): ?>
            <div class="alert alert-info">Usuário atualizado com sucesso!</div>
        <?php elseif (isset($_GET['excluido'])): ?>
            <div class="alert alert-danger">Usuário excluído com sucesso!</div>
        <?php endif; ?>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Permissão</th>
                    <th>Imobiliária</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista as $u): ?>
                <tr>
                    <td><?= $u['nome'] ?></td>
                    <td><?= $u['email'] ?></td>
                    <td><?= $u['permissao'] ?></td>
                    <td><?= $u['nome_imobiliaria'] ?? '---' ?></td>
                    <td>
                        <a href="editar.php?id=<?= $u['id_usuario'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="../../controllers/UsuarioController.php?excluir=<?= $u['id_usuario'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Confirma exclusão?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
