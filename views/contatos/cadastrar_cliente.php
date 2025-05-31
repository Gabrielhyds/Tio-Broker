<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Cliente</title>
</head>
<body>

<div class="container mt-4">
    <h2>Cadastrar Cliente</h2>

    <form method="POST" action="index.php?controller=cliente&action=cadastrar">
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label>Número de telefone</label>
                <input type="text" name="numero" class="form-control" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>CPF</label>
                <input type="text" name="cpf" class="form-control">
            </div>
            <div class="col-md-8">
                <label>Empreendimento</label>
                <input type="text" name="empreendimento" class="form-control">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label>Renda</label>
                <input type="number" step="0.01" name="renda" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Entrada</label>
                <input type="number" step="0.01" name="entrada" class="form-control">
            </div>
            <div class="col-md-3">
                <label>FGTS</label>
                <input type="number" step="0.01" name="fgts" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Subsídio</label>
                <input type="number" step="0.01" name="subsidio" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label>Foto (URL)</label>
            <input type="text" name="foto" class="form-control">
        </div>

        <div class="mb-3">
            <label>Tipo de Lista</label>
            <select name="tipo_lista" class="form-select">
                <option value="Potencial">Potencial</option>
                <option value="Não potencial">Não potencial</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Cadastrar</button>
        <a href="index.php?controller=cliente&action=listar" class="btn btn-secondary">Cancelar</a>
    </form>
</div>


</body>
</html>