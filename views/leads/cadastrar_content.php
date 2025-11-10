<?php
/**
 * Arquivo de conteúdo: cadastrar_lead_content.php
 * Incluído dentro do layout 'template_base.php'.
 * 
 * Variáveis disponíveis:
 *  - $usuarios
 *  - $id_usuario_logado
 *  - $nome_usuario_logado
 */

// Permissão real para atribuir leads (Admin ou Coordenador)
$pode_atribuir = isset($_SESSION['usuario']['permissao']) &&
                 in_array($_SESSION['usuario']['permissao'], ['Admin', 'Coordenador', 'SuperAdmin']);
?>

<div class="container mx-auto p-4 md:p-6 lg:p-8 max-w-3xl">

    <!-- Cabeçalho -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-slate-800">
            <?= htmlspecialchars($pageTitle ?? 'Cadastrar Lead') ?>
        </h1>
        <a href="pipeline.php" 
           class="inline-flex items-center gap-2 bg-slate-200 text-slate-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-300 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Voltar ao Pipeline
        </a>
    </div>

    <!-- Alertas -->
    <?php if (!empty($_SESSION['erro']) || !empty($_SESSION['sucesso'])): ?>
        <?php 
            $tipo = !empty($_SESSION['erro']) ? 'erro' : 'sucesso';
            $msg = htmlspecialchars($_SESSION[$tipo]);
            $cor = $tipo === 'erro' ? 'red' : 'green';
            unset($_SESSION[$tipo]);
        ?>
        <div class="bg-<?= $cor ?>-100 border border-<?= $cor ?>-400 text-<?= $cor ?>-700 px-4 py-3 rounded-lg relative mb-5 alert-msg" role="alert">
            <strong class="font-bold"><?= ucfirst($tipo) ?>!</strong>
            <span class="block sm:inline"><?= $msg ?></span>
        </div>
    <?php endif; ?>

    <!-- Formulário -->
    <div class="bg-white p-6 md:p-8 rounded-lg shadow-sm">
        <form action="../../controllers/LeadController.php" method="POST" class="space-y-6" id="formLead">
            <input type="hidden" name="action" value="cadastrar">

            <!-- Nome -->
            <div>
                <label for="nome" class="block text-sm font-medium text-slate-600 mb-1">
                    Nome Completo <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nome" id="nome" 
                       class="w-full border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                       placeholder="Nome do lead" required>
            </div>

            <!-- Telefone e E-mail -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="telefone" class="block text-sm font-medium text-slate-600 mb-1">
                        Telefone (Principal) <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="telefone" id="telefone" maxlength="15"
                           class="w-full border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                           placeholder="(11) 99999-9999" required>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-600 mb-1">E-mail</label>
                    <input type="email" name="email" id="email" 
                           class="w-full border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                           placeholder="email@exemplo.com">
                </div>
            </div>

            <!-- Contato Secundário e Origem -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="contato_secundario" class="block text-sm font-medium text-slate-600 mb-1">
                        Telefone (Secundário)
                    </label>
                    <input type="tel" name="contato_secundario" id="contato_secundario" maxlength="15"
                           class="w-full border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                           placeholder="Opcional">
                </div>
                <div>
                    <label for="origem" class="block text-sm font-medium text-slate-600 mb-1">
                        Origem do Lead <span class="text-red-500">*</span>
                    </label>
                    <select name="origem" id="origem" 
                            class="w-full border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">Selecione...</option>
                        <?php 
                        $origens = ['Portal Imobiliário', 'Redes Sociais', 'Indicação', 'Site Próprio', 'Feirão', 'Outro'];
                        foreach ($origens as $origem): ?>
                            <option value="<?= htmlspecialchars($origem) ?>"><?= htmlspecialchars($origem) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Interesse -->
            <div>
                <label for="interesse" class="block text-sm font-medium text-slate-600 mb-1">Interesse</label>
                <input type="text" name="interesse" id="interesse"
                       class="w-full border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                       placeholder="Ex: Apartamento 3 dorms na região X">
            </div>

            <!-- Observações -->
            <div>
                <label for="observacoes" class="block text-sm font-medium text-slate-600 mb-1">Observações</label>
                <textarea name="observacoes" id="observacoes" rows="4"
                          class="w-full border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                          placeholder="Detalhes sobre o primeiro contato, perfil do cliente, etc."></textarea>
            </div>

            <!-- Responsável -->
            <div>
                <label for="id_usuario_responsavel" class="block text-sm font-medium text-slate-600 mb-1">
                    Responsável pelo Lead <span class="text-red-500">*</span>
                </label>

                <?php if ($pode_atribuir): ?>
                    <select name="id_usuario_responsavel" id="id_usuario_responsavel" 
                            class="w-full border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">Selecione um corretor...</option>
                        <?php foreach ($usuarios as $u): ?>
                            <option value="<?= htmlspecialchars($u['id_usuario']) ?>" 
                                <?= $u['id_usuario'] == $id_usuario_logado ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input type="hidden" name="id_usuario_responsavel" value="<?= htmlspecialchars($id_usuario_logado) ?>">
                    <input type="text" value="<?= htmlspecialchars($nome_usuario_logado) ?>" 
                           disabled class="w-full border-slate-300 rounded-lg px-3 py-2 bg-slate-100 text-slate-500 cursor-not-allowed">
                <?php endif; ?>
            </div>

            <!-- Botão -->
            <div class="flex justify-end pt-4 border-t border-slate-200">
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition-all shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Salvar Lead
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Esconde alerta após 4s
    const alert = document.querySelector('.alert-msg');
    if (alert) {
        setTimeout(() => {
            alert.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(() => alert.remove(), 600);
        }, 4000);
    }

    // Máscara simples de telefone
    const aplicarMascara = (input) => {
        input.addEventListener('input', (e) => {
            let v = e.target.value.replace(/\D/g, '');
            v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
            v = v.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = v.slice(0, 15);
        });
    };
    aplicarMascara(document.getElementById('telefone'));
    aplicarMascara(document.getElementById('contato_secundario'));

    // Validação leve de campos obrigatórios
    document.getElementById('formLead').addEventListener('submit', (e) => {
        const nome = document.getElementById('nome').value.trim();
        const telefone = document.getElementById('telefone').value.trim();
        const origem = document.getElementById('origem').value.trim();

        if (!nome || !telefone || !origem) {
            e.preventDefault();
            alert('Preencha todos os campos obrigatórios.');
        }
    });
});
</script>
