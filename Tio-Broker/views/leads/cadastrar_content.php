<?php
/**
 * Este é o arquivo de CONTEÚDO (cadastrar_lead_content.php)
 * Ele é incluído dentro do 'template_base.php' (layout principal).
 * As variáveis $usuarios, $id_usuario_logado, $nome_usuario_logado vêm de 'cadastrar_lead.php'.
 */

// Define se o usuário pode atribuir leads (RF06)
// TODO: Basear isso na permissão real (ex: Admin ou Coordenador)
$pode_atribuir = true; //($_SESSION['usuario']['permissao'] == 'Admin' || $_SESSION['usuario']['permissao'] == 'Coordenador');
?>

<div class="container mx-auto p-4 md:p-6 lg:p-8 max-w-3xl">

    <!-- CABEÇALHO -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-slate-800"><?= $pageTitle ?? 'Cadastrar Lead' ?></h1>
        <a href="pipeline.php" class="inline-flex items-center gap-2 bg-slate-200 text-slate-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-300 transition-colors">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Voltar ao Pipeline
        </a>
    </div>

    <!-- Mensagens de Erro/Sucesso (vêm da sessão) -->
    <?php if (isset($_SESSION['erro'])): ?>
         <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-5" role="alert">
            <strong class="font-bold">Erro!</strong>
            <span class="block sm:inline"><?= htmlspecialchars($_SESSION['erro']); ?></span>
        </div>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <!-- CONTAINER DO FORMULÁRIO -->
    <div class="bg-white p-6 md:p-8 rounded-lg shadow-sm">
        
        <form action="../../controllers/LeadController.php" method="POST" class="space-y-6">
            <input type="hidden" name="action" value="cadastrar">

            <!-- Nome (Obrigatório) -->
            <div>
                <label for="nome" class="block text-sm font-medium text-slate-600 mb-1">Nome Completo <span class="text-red-500">*</span></label>
                <input type="text" name="nome" id="nome" class="w-full border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nome do lead" required>
            </div>

            <!-- Campos Lado a Lado: Telefone (Obrigatório) e E-mail -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="telefone" class="block text-sm font-medium text-slate-600 mb-1">Telefone (Principal) <span class="text-red-500">*</span></label>
                    <input type="tel" name="telefone" id="telefone" class="w-full border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="(11) 99999-9999" required>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-600 mb-1">E-mail</label>
                    <input type="email" name="email" id="email" class="w-full border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="email@exemplo.com">
                </div>
            </div>

            <!-- Campos Lado a Lado: Contato Secundário e Origem (Obrigatório) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="contato_secundario" class="block text-sm font-medium text-slate-600 mb-1">Telefone (Secundário)</label>
                    <input type="tel" name="contato_secundario" id="contato_secundario" class="w-full border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Opcional">
                </div>
                <div>
                    <label for="origem" class="block text-sm font-medium text-slate-600 mb-1">Origem do Lead <span class="text-red-500">*</span></label>
                    <select name="origem" id="origem" class="w-full border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">Selecione...</option>
                        <option value="Portal Imobiliário">Portal Imobiliário</option>
                        <option value="Redes Sociais">Redes Sociais</option>
                        <option value="Indicação">Indicação</option>
                        <option value="Site Próprio">Site Próprio</option>
                        <option value="Feirão">Feirão</option>
                        <option value="Outro">Outro</option>
                    </select>
                </div>
            </div>

            <!-- Interesse -->
            <div>
                <label for="interesse" class="block text-sm font-medium text-slate-600 mb-1">Interesse</label>
                <input type="text" name="interesse" id="interesse" class="w-full border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: Apartamento 3 dorms na região X">
            </div>

            <!-- Observações -->
            <div>
                <label for="observacoes" class="block text-sm font-medium text-slate-600 mb-1">Observações</label>
                <textarea name="observacoes" id="observacoes" class="w-full border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" rows="4" placeholder="Detalhes sobre o primeiro contato, perfil do cliente, etc."></textarea>
            </div>

            <!-- Responsável (RF06) -->
            <div>
                <label for="id_usuario_responsavel" class="block text-sm font-medium text-slate-600 mb-1">Responsável pelo Lead <span class="text-red-500">*</span></label>
                
                <?php if ($pode_atribuir): ?>
                    <!-- Se for Admin/Coordenador, mostra um Select -->
                    <select name="id_usuario_responsavel" id="id_usuario_responsavel" class="w-full border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">Selecione um corretor...</option>
                        <option value="<?= $id_usuario_logado ?>" selected>Eu (<?= htmlspecialchars($nome_usuario_logado) ?>)</option>
                        <?php foreach ($usuarios as $u): ?>
                            <?php if($u['id_usuario'] != $id_usuario_logado): // Evita duplicar o usuário logado ?>
                                <option value="<?= $u['id_usuario'] ?>"><?= htmlspecialchars($u['nome']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <!-- Se for Corretor, atribui a ele mesmo -->
                    <input type="hidden" name="id_usuario_responsavel" value="<?= $id_usuario_logado ?>">
                    <input type="text" value="<?= htmlspecialchars($nome_usuario_logado) ?>" disabled class="w-full border-slate-300 rounded-lg px-3 py-2 bg-slate-100 text-slate-500 cursor-not-allowed">
                <?php endif; ?>
            </div>


            <!-- Botão -->
            <div class="flex justify-end pt-4 border-t border-slate-200">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm">
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
document.addEventListener('DOMContentLoaded', function() {
    // Esconde o alerta de erro após 5 segundos
    const errorAlert = document.querySelector('.bg-red-100');
    if (errorAlert) {
        setTimeout(() => {
            errorAlert.style.transition = 'opacity 0.5s ease';
            errorAlert.style.opacity = '0';
            setTimeout(() => errorAlert.remove(), 500);
        }, 5000);
    }
});
</script>
