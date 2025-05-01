<?php
require_once '../../config/config.php';
require_once '../../models/Imobiliaria.php';

$imobiliaria = new Imobiliaria($connection);
$listaImobiliarias = $imobiliaria->listarTodas();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Cadastrar Novo Usuário</h2>
        <form action="../../controllers/UsuarioController.php" method="POST">
            <input type="hidden" name="action" value="cadastrar">
            <div class="mb-3">
                <label>Nome:</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>CPF:</label>
                <input type="text" name="cpf" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Telefone:</label>
                <input type="text" name="telefone" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Senha:</label>
                <input type="password" name="senha" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Permissão:</label>
                <select name="permissao" class="form-select" required>
                    <option value="Admin">Admin</option>
                    <option value="Coordenador">Coordenador</option>
                    <option value="Corretor">Corretor</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Imobiliária:</label>
                <select name="id_imobiliaria" class="form-select" required>
                    <?php foreach ($listaImobiliarias as $i): ?>
                        <option value="<?= $i['id_imobiliaria'] ?>"><?= $i['nome'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>
</body>
</html>