<?php
require_once '../../config/config.php';
require_once '../../models/Tarefa.php';
require_once '../../models/Cliente.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}

$tarefaModel = new Tarefa($connection);
$clienteModel = new Cliente($connection);

$id_tarefa = $_GET['id'] ?? null;
if (!$id_tarefa) {
    $_SESSION['erro'] = 'Tarefa não encontrada.';
    header('Location: listar_tarefa.php');
    exit;
}

$tarefa = $tarefaModel->buscarPorId($id_tarefa);
$clientes = $clienteModel->listarTodos();

$id_usuario_logado = $_SESSION['usuario']['id_usuario'];
$nome_usuario_logado = $_SESSION['usuario']['nome'];

?>

<h2 class="text-2xl font-semibold mb-4">Editar Tarefa</h2>

<?php if (isset($_SESSION['erro'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
        <?= htmlspecialchars($_SESSION['erro']);
        unset($_SESSION['erro']); ?>
    </div>
<?php endif; ?>

<form action="../../controllers/TarefaController.php" method="POST" class="space-y-4" onsubmit="return validarFormulario()">
    <input type="hidden" name="action" value="editar">
    <input type="hidden" name="id_tarefa" value="<?= $tarefa['id_tarefa'] ?>">
    <input type="hidden" name="id_usuario" value="<?= $id_usuario_logado ?>">

    <!-- Responsável -->
    <div>
        <label class="block font-medium text-gray-700">Responsável</label>
        <input type="text" value="<?= htmlspecialchars($nome_usuario_logado) ?>" disabled
            class="w-full border border-gray-300 rounded p-2 bg-gray-100 text-gray-600 cursor-not-allowed">
    </div>

    <!-- Tipo de Tarefa -->
    <div>
        <label class="block font-medium text-gray-700">Tipo de Tarefa</label>
        <select name="tipo_tarefa" id="tipo_tarefa" class="w-full border border-gray-300 rounded p-2" onchange="alternarTipo()" required>
            <option value="">Selecione</option>
            <option value="cliente" <?= $tarefa['id_cliente'] ? 'selected' : '' ?>>Cliente</option>
            <option value="outro" <?= !$tarefa['id_cliente'] ? 'selected' : '' ?>>Outro</option>
        </select>
    </div>

    <!-- Cliente -->
    <div id="campo_cliente" class="<?= $tarefa['id_cliente'] ? '' : 'hidden' ?>">
        <label class="block font-medium text-gray-700">Cliente</label>
        <select name="id_cliente" class="w-full border border-gray-300 rounded p-2">
            <option value="">Selecione</option>
            <?php foreach ($clientes as $c): ?>
                <option value="<?= $c['id_cliente'] ?>" <?= $tarefa['id_cliente'] == $c['id_cliente'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Outro Tipo -->
    <div id="campo_outro" class="<?= $tarefa['id_cliente'] ? 'hidden' : '' ?>">
        <label class="block font-medium text-gray-700">Outro Tipo</label>
        <input type="text" name="outro_tipo" value="<?= !$tarefa['id_cliente'] ? htmlspecialchars($tarefa['descricao']) : '' ?>" class="w-full border border-gray-300 rounded p-2">
    </div>

    <!-- Descrição -->
    <div>
        <label class="block font-medium text-gray-700">Descrição</label>
        <textarea name="descricao" class="w-full border border-gray-300 rounded p-2" rows="3" required><?= htmlspecialchars($tarefa['descricao']) ?></textarea>
    </div>

    <!-- Status -->
    <div>
        <label class="block font-medium text-gray-700">Status</label>
        <select name="status" class="w-full border border-gray-300 rounded p-2" required>
            <option value="pendente" <?= $tarefa['status'] === 'pendente' ? 'selected' : '' ?>>Pendente</option>
            <option value="em andamento" <?= $tarefa['status'] === 'em andamento' ? 'selected' : '' ?>>Em andamento</option>
            <option value="concluida" <?= $tarefa['status'] === 'concluida' ? 'selected' : '' ?>>Concluída</option>
        </select>
    </div>

    <!-- Prioridade -->
    <div>
        <label class="block font-medium text-gray-700">Prioridade</label>
        <select name="prioridade" class="w-full border border-gray-300 rounded p-2">
            <option value="baixa" <?= $tarefa['prioridade'] === 'baixa' ? 'selected' : '' ?>>Baixa</option>
            <option value="media" <?= $tarefa['prioridade'] === 'media' ? 'selected' : '' ?>>Média</option>
            <option value="alta" <?= $tarefa['prioridade'] === 'alta' ? 'selected' : '' ?>>Alta</option>
        </select>
    </div>

    <!-- Prazo -->
    <div>
        <label class="block font-medium text-gray-700">Prazo</label>
        <input type="date" name="prazo" class="w-full border border-gray-300 rounded p-2" value="<?= $tarefa['prazo'] ?>">
    </div>

    <div class="flex justify-end">
        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium transition">
            <i class="fas fa-save mr-2"></i>Atualizar
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