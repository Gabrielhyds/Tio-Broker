<?php
session_start();
require_once '../../config/config.php';
require_once '../../models/Imobiliaria.php';

$imobiliaria = new Imobiliaria($connection);
$dados = $imobiliaria->buscarPorId($_GET['id']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Imobiliária</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Editar Imobiliária</h2>
        <form action="../../controllers/ImobiliariaController.php" method="POST">
            <input type="hidden" name="action" value="atualizar">
            <input type="hidden" name="id" value="<?= $dados['id_imobiliaria'] ?>">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($dados['nome']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="cnpj" class="form-label">CNPJ</label>
                <input type="text" class="form-control" name="cnpj" value="<?= htmlspecialchars($dados['cnpj']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
