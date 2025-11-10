<?php
// session_start() deve ser chamado no início do seu script.
// session_start();

/**
 * -------------------------------------------------------------------------
 * NOTA: As variáveis abaixo ($clientes, $_SESSION, etc.)
 * devem ser populadas pelo seu sistema.
 * -------------------------------------------------------------------------
 */

// --- Mock de Dados para Demonstração (REMOVA EM PRODUÇÃO) ---
// Este bloco serve apenas para que o template possa ser visualizado.
if (!isset($_SESSION['usuario'])) {
    $_SESSION['usuario'] = ['id_usuario' => 2, 'nome' => 'Bruno'];
}
if (!isset($clientes)) {
    $clientes = [
        ['id_cliente' => 1, 'nome' => 'Empresa X'],
        ['id_cliente' => 2, 'nome' => 'Projeto Y'],
        ['id_cliente' => 3, 'nome' => 'Startup Z']
    ];
}
// Descomente para testar a mensagem de erro.
// $_SESSION['erro'] = 'Ocorreu um erro ao tentar salvar. Verifique os dados.';
// --- Fim do Mock de Dados ---

$id_usuario_logado = $_SESSION['usuario']['id_usuario'] ?? '';
$nome_usuario_logado = $_SESSION['usuario']['nome'] ?? 'Usuário Desconhecido';

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Tarefa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        @keyframes slideInUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideOutDown {
            from {
                transform: translateY(0);
                opacity: 1;
            }

            to {
                transform: translateY(100%);
                opacity: 0;
            }
        }

        .toast-enter {
            animation: slideInUp 0.3s ease-out forwards;
        }

        .toast-exit {
            animation: slideOutDown 0.3s ease-in forwards;
        }
    </style>
</head>

<body class="text-slate-700">

    <div class="container mx-auto p-4 md:p-6 lg:p-8 max-w-3xl">

        <!-- CABEÇALHO -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-slate-800">Cadastrar Tarefa</h1>
            <a href="listar_tarefa.php" class="inline-flex items-center gap-2 bg-slate-200 text-slate-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-300 transition-colors">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Voltar
            </a>
        </div>

        <!-- CONTAINER DO FORMULÁRIO -->
        <div class="bg-white p-6 md:p-8 rounded-lg shadow-sm">


            <form action="../../controllers/TarefaController.php" method="POST" class="space-y-6" onsubmit="return validarFormulario()">
                <input type="hidden" name="action" value="cadastrar">

                <!-- Campos Lado a Lado: Responsável e Tipo de Tarefa -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Responsável (fixo) -->
                    <div>
                        <label for="nome_usuario_logado" class="block text-sm font-medium text-slate-600 mb-1">Responsável</label>
                        <input type="hidden" name="id_usuario" value="<?= $id_usuario_logado ?>">
                        <input type="text" id="nome_usuario_logado" value="<?= htmlspecialchars($nome_usuario_logado) ?>" disabled
                            class="w-full border-slate-300 rounded-lg px-3 py-2 bg-slate-100 text-slate-500 cursor-not-allowed focus:ring-0 focus:border-slate-300">
                    </div>

                    <!-- Tipo de Tarefa -->
                    <div>
                        <label for="tipo_tarefa" class="block text-sm font-medium text-slate-600 mb-1">Tipo de Tarefa <span class="text-red-500">*</span></label>
                        <select name="tipo_tarefa" id="tipo_tarefa" class="w-full border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" onchange="alternarTipo()">
                            <option value="">Selecione...</option>
                            <option value="cliente">Cliente</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                </div>

                <!-- Campos Condicionais -->
                <div id="campos_condicionais">
                    <!-- Cliente -->
                    <div id="campo_cliente" class="hidden mt-6">
                        <label for="id_cliente" class="block text-sm font-medium text-slate-600 mb-1">Cliente <span class="text-red-500">*</span></label>
                        <select name="id_cliente" id="id_cliente" class="w-full border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="">Selecione um cliente...</option>
                            <?php foreach ($clientes as $c): ?>
                                <option value="<?= $c['id_cliente'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Outro tipo -->
                    <div id="campo_outro" class="hidden mt-6">
                        <label for="outro_tipo" class="block text-sm font-medium text-slate-600 mb-1">Nome do Cliente/Projeto <span class="text-red-500">*</span></label>
                        <input type="text" name="outro_tipo" id="outro_tipo" class="w-full border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" placeholder="Descreva o cliente ou projeto">
                    </div>
                </div>

                <!-- Descrição -->
                <div>
                    <label for="descricao" class="block text-sm font-medium text-slate-600 mb-1">Descrição <span class="text-red-500">*</span></label>
                    <textarea name="descricao" id="descricao" class="w-full border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" rows="4" placeholder="Detalhe a tarefa a ser realizada..."></textarea>
                </div>

                <!-- Campos Lado a Lado: Status, Prioridade e Prazo -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-600 mb-1">Status</label>
                        <select name="status" id="status" class="w-full border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="pendente">Pendente</option>
                            <option value="em andamento">Em andamento</option>
                            <option value="concluida">Concluída</option>
                        </select>
                    </div>
                    <!-- Prioridade -->
                    <div>
                        <label for="prioridade" class="block text-sm font-medium text-slate-600 mb-1">Prioridade</label>
                        <select name="prioridade" id="prioridade" class="w-full border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="baixa">Baixa</option>
                            <option value="media" selected>Média</option>
                            <option value="alta">Alta</option>
                        </select>
                    </div>
                    <!-- Prazo -->
                    <div>
                        <label for="prazo" class="block text-sm font-medium text-slate-600 mb-1">Prazo</label>
                        <input type="date" name="prazo" id="prazo" class="w-full border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>

                <!-- Botão -->
                <div class="flex justify-end pt-4 border-t border-slate-200">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Salvar Tarefa
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="toast" class="fixed bottom-5 right-5 w-80 p-4 rounded-lg shadow-lg text-white hidden z-50">
        <p id="toast-message"></p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const campoCliente = document.getElementById('campo_cliente');
            const selectCliente = document.getElementById('id_cliente');
            const campoOutro = document.getElementById('campo_outro');
            const inputOutro = document.getElementById('outro_tipo');
            const inputDescricao = document.getElementById('descricao');

            // Função para mostrar/ocultar campos com animação
            window.alternarTipo = function() {
                const tipo = document.getElementById('tipo_tarefa').value;

                // Esconde ambos os campos primeiro
                campoCliente.classList.add('hidden');
                campoOutro.classList.add('hidden');

                // Remove o 'required' para evitar bloqueio do formulário
                selectCliente.required = false;
                inputOutro.required = false;

                if (tipo === 'cliente') {
                    campoCliente.classList.remove('hidden');
                    selectCliente.required = true;
                } else if (tipo === 'outro') {
                    campoOutro.classList.remove('hidden');
                    inputOutro.required = true;
                }
            }

            // --- LÓGICA DO TOAST ---
            let toastTimeout;
            window.showToast = function(message, type = 'error') {
                const toast = document.getElementById('toast');
                const toastMessage = document.getElementById('toast-message');
                toastMessage.textContent = message;
                toast.className = 'fixed bottom-5 right-5 w-80 p-4 rounded-lg shadow-lg text-white z-50';
                toast.classList.add(type === 'success' ? 'bg-green-500' : 'bg-red-500');
                clearTimeout(toastTimeout);
                toast.classList.remove('hidden', 'toast-exit');
                toast.classList.add('toast-enter');
                toastTimeout = setTimeout(() => {
                    toast.classList.remove('toast-enter');
                    toast.classList.add('toast-exit');
                    setTimeout(() => toast.classList.add('hidden'), 300);
                }, 4000);
            }

            // --- VALIDAÇÃO DO FORMULÁRIO ---
            window.validarFormulario = function() {
                const tipo = document.getElementById('tipo_tarefa').value;

                if (tipo === '') {
                    showToast('Por favor, selecione o tipo da tarefa.');
                    return false;
                }
                if (tipo === 'cliente' && selectCliente.value === '') {
                    showToast('Por favor, selecione um cliente.');
                    return false;
                }
                if (tipo === 'outro' && inputOutro.value.trim() === '') {
                    showToast('Por favor, descreva o cliente ou projeto.');
                    return false;
                }
                if (inputDescricao.value.trim() === '') {
                    showToast('A descrição da tarefa é obrigatória.');
                    return false;
                }
                return true;
            }

            // Ocultar alerta de erro após alguns segundos
            const errorAlert = document.getElementById('error-alert');
            if (errorAlert) {
                setTimeout(() => {
                    errorAlert.style.transition = 'opacity 0.5s ease';
                    errorAlert.style.opacity = '0';
                    setTimeout(() => errorAlert.remove(), 500);
                }, 5000);
            }
        });
    </script>
</body>

</html>