<h2 class="text-2xl font-semibold mb-4">Cadastrar Tarefa</h2>

<?php if (isset($_SESSION['erro'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
        <?= htmlspecialchars($_SESSION['erro']);
        unset($_SESSION['erro']); ?>
    </div>
<?php endif; ?>

<form action="../../controllers/TarefaController.php" method="POST" class="space-y-4" onsubmit="return validarFormulario()">
    <input type="hidden" name="action" value="cadastrar">

    <?php
    $id_usuario_logado = $_SESSION['usuario']['id_usuario'];
    $nome_usuario_logado = $_SESSION['usuario']['nome'];
    ?>

    <!-- Responsável (fixo) -->
    <div>
        <label class="block text-sm font-medium text-gray-700">Responsável</label>
        <input type="hidden" name="id_usuario" value="<?= $id_usuario_logado ?>">
        <input type="text" value="<?= htmlspecialchars($nome_usuario_logado) ?>" disabled
            class="w-full border border-gray-300 rounded px-4 py-2 bg-gray-100 text-gray-600 cursor-not-allowed">
    </div>

    <!-- Tipo de Tarefa -->
    <div>
        <label class="block text-sm font-medium text-gray-700">Tipo de Tarefa</label>
        <select name="tipo_tarefa" id="tipo_tarefa" class="w-full border border-gray-300 rounded px-4 py-2" onchange="alternarTipo()" required>
            <option value="">Selecione</option>
            <option value="cliente">Cliente</option>
            <option value="outro">Outro</option>
        </select>
    </div>

    <!-- Cliente -->
    <div id="campo_cliente" class="hidden">
        <label class="block text-sm font-medium text-gray-700">Cliente</label>
        <select name="id_cliente" class="w-full border border-gray-300 rounded px-4 py-2">
            <option value="">Selecione</option>
            <?php foreach ($clientes as $c): ?>
                <option value="<?= $c['id_cliente'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Outro tipo -->
    <div id="campo_outro" class="hidden">
        <label class="block text-sm font-medium text-gray-700">Outro Tipo</label>
        <input type="text" name="outro_tipo" class="w-full border border-gray-300 rounded px-4 py-2" placeholder="Descreva o tipo de tarefa">
    </div>

    <!-- Descrição -->
    <div>
        <label class="block text-sm font-medium text-gray-700">Descrição</label>
        <textarea name="descricao" class="w-full border border-gray-300 rounded px-4 py-2" rows="3" required></textarea>
    </div>

    <!-- Status -->
    <div>
        <label class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" class="w-full border border-gray-300 rounded px-4 py-2" required>
            <option value="pendente">Pendente</option>
            <option value="em andamento">Em andamento</option>
            <option value="concluida">Concluída</option>
        </select>
    </div>

    <!-- Prioridade -->
    <div>
        <label class="block text-sm font-medium text-gray-700">Prioridade</label>
        <select name="prioridade" class="w-full border border-gray-300 rounded px-4 py-2">
            <option value="baixa">Baixa</option>
            <option value="media" selected>Média</option>
            <option value="alta">Alta</option>
        </select>
    </div>

    <!-- Prazo -->
    <div>
        <label class="block text-sm font-medium text-gray-700">Prazo</label>
        <input type="date" name="prazo" class="w-full border border-gray-300 rounded px-4 py-2">
    </div>

    <!-- Botão -->
    <div class="flex justify-end">
        <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-medium transition">
            <i class="fas fa-check mr-2"></i>Salvar
        </button>
    </div>
</form>

<script>
    function alternarTipo() {
        const tipo = document.getElementById('tipo_tarefa').value;
        document.getElementById('campo_cliente').classList.add('hidden');
        document.getElementById('campo_outro').classList.add('hidden');

        if (tipo === 'cliente') {
            document.getElementById('campo_cliente').classList.remove('hidden');
        } else if (tipo === 'outro') {
            document.getElementById('campo_outro').classList.remove('hidden');
        }
    }

    function validarFormulario() {
        const tipo = document.getElementById('tipo_tarefa').value;
        if (tipo === '') {
            alert('Selecione o tipo da tarefa!');
            return false;
        }
        if (tipo === 'cliente') {
            const cliente = document.querySelector('[name="id_cliente"]').value;
            if (!cliente) {
                alert('Selecione um cliente.');
                return false;
            }
        }
        if (tipo === 'outro') {
            const outro = document.querySelector('[name="outro_tipo"]').value;
            if (!outro.trim()) {
                alert('Preencha o campo de outro tipo.');
                return false;
            }
        }
        return true;
    }
</script>