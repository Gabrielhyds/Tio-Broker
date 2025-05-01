<?php
session_start();
require_once '../../config/config.php';
require_once '../../models/Imobiliaria.php';

$imobiliaria = new Imobiliaria($connection);
$lista = $imobiliaria->listarTodas();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Imobiliárias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-white">
    <div class="container mt-5">
        <h2 class="mb-4">Imobiliárias Cadastradas</h2>
        <?php if (isset($_GET['sucesso'])): ?>
            <div class="alert alert-success">Imobiliária cadastrada com sucesso!</div>
        <?php elseif (isset($_GET['atualizado'])): ?>
            <div class="alert alert-info">Imobiliária atualizada com sucesso!</div>
        <?php elseif (isset($_GET['excluido'])): ?>
            <div class="alert alert-danger">Imobiliária excluída com sucesso!</div>
        <?php endif; ?>
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>CNPJ</th>
                    <th>Usuários</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($lista): foreach ($lista as $item): ?>
                    <tr>
                        <td><?= $item['id_imobiliaria'] ?></td>
                        <td><?= htmlspecialchars($item['nome']) ?></td>
                        <td><?= htmlspecialchars($item['cnpj']) ?></td>
                        <td><?= $item['total_usuarios'] ?></td>
                        <td>
                            <a href="editar.php?id=<?= $item['id_imobiliaria'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="../../controllers/ImobiliariaController.php?excluir=<?= $item['id_imobiliaria'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Deseja realmente excluir?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="5" class="text-center">Nenhuma imobiliária encontrada.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>