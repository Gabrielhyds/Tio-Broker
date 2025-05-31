<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Clientes</h2>
        <a href="index.php?controller=cliente&action=cadastrar" class="btn btn-primary">+ Novo Cliente</a>
    </div>

    <?php if (empty($clientes)): ?>
        <div class="alert alert-info">Nenhum cliente cadastrado.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Nome</th>
                        <th>Número</th>
                        <th>CPF</th>
                        <th>Empreendimento</th>
                        <th>Renda</th>
                        <th>Entrada</th>
                        <th>FGTS</th>
                        <th>Subsídio</th>
                        <th>Tipo</th>
                        <th>Foto</th>
                        <th>Cadastro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?= htmlspecialchars($cliente['nome']) ?></td>
                            <td><?= htmlspecialchars($cliente['numero']) ?></td>
                            <td><?= htmlspecialchars($cliente['cpf']) ?></td>
                            <td><?= htmlspecialchars($cliente['empreendimento']) ?></td>
                            <td>R$ <?= number_format($cliente['renda'], 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($cliente['entrada'], 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($cliente['fgts'], 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($cliente['subsidio'], 2, ',', '.') ?></td>
                            <td><?= $cliente['tipo_lista'] ?></td>
                            <td>
                                <?php if (!empty($cliente['foto'])): ?>
                                    <img src="<?= htmlspecialchars($cliente['foto']) ?>" width="60">
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($cliente['criado_em'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>


</body>
</html>