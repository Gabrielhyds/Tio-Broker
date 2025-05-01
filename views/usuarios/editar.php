<?php
require_once '../../config/config.php';
require_once '../../models/Usuario.php';
require_once '../../models/Imobiliaria.php';

$usuario = new Usuario($connection);
$dados = $usuario->buscarPorId($_GET['id']);

$imobiliaria = new Imobiliaria($connection);
$listaImobiliarias = $imobiliaria->listarTodas();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Editar Usuário</h2>
        <form action="../../controllers/UsuarioController.php" method="POST">
            <input type="hidden" name="action" value="atualizar">
            <input type="hidden" name="id_usuario" value="<?= $dados['id_usuario'] ?>">
            <div class="mb-3">
                <label>Nome:</label>
                <input type="text" name="nome" class="form-control" value="<?= $dados['nome'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" value="<?= $dados['email'] ?>" required>
            </div>
            <div class="mb-3">
                <label>CPF:</label>
                <input type="text" name="cpf" class="form-control" value="<?= $dados['cpf'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Telefone:</label>
                <input type="text" name="telefone" class="form-control" value="<?= $dados['telefone'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Permissão:</label>
                <select name="permissao" class="form-select" required>
                    <option value="Admin" <?= $dados['permissao'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="Coordenador" <?= $dados['permissao'] === 'Coordenador' ? 'selected' : '' ?>>Coordenador</option>
                    <option value="Corretor" <?= $dados['permissao'] === 'Corretor' ? 'selected' : '' ?>>Corretor</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Imobiliária:</label>
                <select name="id_imobiliaria" class="form-select" required>
                    <?php foreach ($listaImobiliarias as $i): ?>
                        <option value="<?= $i['id_imobiliaria'] ?>" <?= $i['id_imobiliaria'] == $dados['id_imobiliaria'] ? 'selected' : '' ?>><?= $i['nome'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
        </form>
        
    </div>
</body>
</html>
