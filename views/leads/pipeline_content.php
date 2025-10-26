<?php
/**
 * Este é o arquivo de CONTEÚDO (pipeline_content.php)
 * Ele é incluído dentro do 'template_base.php' (layout principal).
 * As variáveis $leadsAgrupados e $usuarios vêm de 'pipeline.php'.
 */

// IDs logados (fornecidos pelo 'pipeline.php' ou sessão)
$id_usuario_logado = $_SESSION['usuario']['id_usuario'] ?? 1;
$nome_usuario_logado = $_SESSION['usuario']['nome'] ?? 'Usuário';

// Permissão (Simplificado) - RF06
// Em um app real, isso viria de uma verificação de permissão
$usuario_e_coordenador = true; // Assumindo que pode atribuir (RF06)

?>

<!-- Estilos CSS específicos para Drag and Drop -->
<style>
    /* Estilo do card sendo arrastado */
    .dragging {
        opacity: 0.5;
        border: 2px dashed #4f46e5;
        transform: scale(1.05);
    }
    /* Estilo da coluna quando um card está sobre ela */
    .drag-over {
        background-color: #f0f4ff; /* Um azul bem claro */
    }
    /* Altura mínima para colunas vazias */
    .pipeline-column .pipeline-cards {
        min-height: 200px;
    }
</style>

<!-- Container Principal do Pipeline -->
<div class="container mx-auto p-4 md:p-6 lg:p-8 max-w-full">

    <!-- CABEÇALHO (RF04, RF07) -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-slate-800">Pipeline de Leads</h1>
        <div class="flex gap-4 w-full md:w-auto">
            <!-- RF07: Buscar e Filtrar -->
            <input type="text" id="filtroBusca" class="w-full md:w-64 border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" placeholder="Buscar por nome, email...">
            
            <!-- RF01: Botão Cadastrar Lead -->
            <button onclick="abrirModalCadastro()" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm whitespace-nowrap">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" /></svg>
                Novo Lead
            </button>
        </div>
    </div>

    <!-- Mensagens de Sucesso e Erro (via Sessão) -->
    <?php if (isset($_SESSION['sucesso'])): ?>
        <div id="toast-sucesso" class="fixed bottom-5 right-5 w-80 p-4 rounded-lg shadow-lg text-white bg-green-500 z-50">
            <p><?= $_SESSION['sucesso']; ?></p>
        </div>
        <?php unset($_SESSION['sucesso']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['erro'])): ?>
         <div id="toast-erro" class="fixed bottom-5 right-5 w-80 p-4 rounded-lg shadow-lg text-white bg-red-500 z-50">
            <p><?= $_SESSION['erro']; ?></p>
        </div>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>


    <!-- Grid do Pipeline (RF04, RF05) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
        
        <?php 
        $colunas = [
            'Novo' => 'bg-blue-100 text-blue-800',
            'Contato' => 'bg-yellow-100 text-yellow-800',
            'Negociação' => 'bg-purple-100 text-purple-800',
            'Fechado' => 'bg-green-100 text-green-800',
            'Perdido' => 'bg-red-100 text-red-800'
        ];
        
        foreach ($colunas as $status => $cor):
            $leadsDaColuna = $leadsAgrupados[$status] ?? [];
            $totalLeads = count($leadsDaColuna);
        ?>
        <!-- Coluna do Pipeline -->
        <div class="pipeline-column bg-slate-50 rounded-lg p-4" 
             ondragover="handleDragOver(event)" 
             ondrop="handleDrop(event, '<?= $status ?>')"
             data-status="<?= $status ?>">
            
            <h2 class="font-semibold text-slate-700 mb-3 flex justify-between items-center">
                <?= $status ?>
                <span class="text-sm font-medium px-2 py-0.5 rounded-full <?= $cor ?>"><?= $totalLeads ?></span>
            </h2>

            <!-- Cards (RF05) -->
            <div class="pipeline-cards space-y-3">
                
                <?php if (empty($leadsDaColuna)): ?>
                    <!-- RF04 Erro: Caso não existam leads (Corrigido 'classs' e adicionado 'pipeline-placeholder') -->
                    <div class="text-center text-slate-400 text-sm p-4 border-2 border-dashed rounded-lg pipeline-placeholder">
                        Arraste leads para cá.
                    </div>
                <?php else: ?>
                    <?php foreach ($leadsDaColuna as $lead): ?>
                    <!-- Card do Lead -->
                    <div class="lead-card bg-white p-4 rounded-lg shadow-sm border border-slate-200 cursor-pointer hover:shadow-md transition-shadow"
                         draggable="true"
                         ondragstart="handleDragStart(event, '<?= $lead['id_lead'] ?>')"
                         onclick="abrirModalDetalhes(<?= $lead['id_lead'] ?>)"
                         data-id="<?= $lead['id_lead'] ?>"
                         data-nome="<?= htmlspecialchars($lead['nome']) ?>"
                         data-email="<?= htmlspecialchars($lead['email']) ?>"> <!-- Para filtro (RF07) -->
                        
                        <h3 class="font-semibold text-sm text-slate-800 mb-1"><?= htmlspecialchars($lead['nome']) ?></h3>
                        <p class="text-xs text-slate-500 mb-2 truncate"><?= htmlspecialchars($lead['interesse']) ?: 'Sem interesse definido' ?></p>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-slate-400"><?= htmlspecialchars($lead['origem']) ?></span>
                            <!-- Responsável (RF06) -->
                            <span class="text-xs font-medium text-indigo-600" title="Responsável: <?= htmlspecialchars($lead['nome_responsavel']) ?: 'Ninguém' ?>">
                                <?= htmlspecialchars(substr($lead['nome_responsavel'] ?? 'N/A', 0, 10)) ?>...
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</div>

<!-- =================================================== -->
<!-- MODAIS (RF01, RF02, RF06, RF08) -->
<!-- =================================================== -->

<!-- FUNDO ESCURO DO MODAL -->
<div id="modal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden" onclick="fecharModais()"></div>

<!-- MODAL: Cadastrar Lead (RF01) -->
<div id="modal-cadastro" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white w-full max-w-lg p-6 md:p-8 rounded-lg shadow-xl z-40 hidden">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Cadastrar Novo Lead</h2>
        <button onclick="fecharModais()" class="text-slate-400 hover:text-slate-600">&times;</button>
    </div>
    
    <form action="../../controllers/LeadController.php" method="POST" class="space-y-4">
        <input type="hidden" name="action" value="cadastrar">
        <!-- Campos obrigatórios (RF01) -->
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Nome <span class="text-red-500">*</span></label>
            <input type="text" name="nome" class="w-full border-slate-300 rounded-lg text-sm" required>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Telefone/Contato <span class="text-red-500">*</span></label>
                <input type="text" name="telefone" class="w-full border-slate-300 rounded-lg text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Email</label>
                <input type="email" name="email" class="w-full border-slate-300 rounded-lg text-sm">
            </div>
        </div>
         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Origem <span class="text-red-500">*</span></label>
                <input type="text" name="origem" class="w-full border-slate-300 rounded-lg text-sm" placeholder="Ex: Facebook, Indicação" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Interesse</label>
                <input type="text" name="interesse" class="w-full border-slate-300 rounded-lg text-sm" placeholder="Ex: Apartamento 2 dorms">
            </div>
        </div>
        <!-- RF06: Atribuir -->
        <?php if ($usuario_e_coordenador): ?>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Responsável</label>
            <select name="id_usuario_responsavel" class="w-full border-slate-300 rounded-lg text-sm">
                <option value="<?= $id_usuario_logado ?>" selected>Eu (<?= htmlspecialchars($nome_usuario_logado) ?>)</option>
                <?php foreach ($usuarios as $u): ?>
                    <?php if ($u['id_usuario'] != $id_usuario_logado): ?>
                    <option value="<?= $u['id_usuario'] ?>"><?= htmlspecialchars($u['nome']) ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
        <?php else: ?>
            <input type="hidden" name="id_usuario_responsavel" value="<?= $id_usuario_logado ?>">
        <?php endif; ?>

        <div class="flex justify-end pt-4 border-t border-slate-200">
            <button type="button" onclick="fecharModais()" class="px-4 py-2 bg-slate-200 text-slate-600 rounded-lg text-sm font-semibold hover:bg-slate-300 transition-colors mr-3">Cancelar</button>
            <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm">Salvar</button>
        </div>
    </form>
</div>

<!-- MODAL: Detalhes / Editar / Interações (RF02, RF08) -->
<div id="modal-detalhes" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white w-full max-w-2xl p-6 md:p-8 rounded-lg shadow-xl z-40 hidden">
    <!-- Conteúdo do modal será preenchido via JS (fetch) -->
    <div id="modal-detalhes-content">
        <!-- Loading -->
        <div class="text-center p-8">
            <p class="text-slate-500">Carregando dados do lead...</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- =================================================== -->
<!-- SCRIPTS JAVASCRIPT -->
<!-- =================================================== -->
<script>
    // --- URLs da API (Controller) ---
    const CONTROLLER_URL = '../../controllers/LeadController.php';

    // --- Seletores de Elementos ---
    const modalBackdrop = document.getElementById('modal-backdrop');
    const modalCadastro = document.getElementById('modal-cadastro');
    const modalDetalhes = document.getElementById('modal-detalhes');
    const modalDetalhesContent = document.getElementById('modal-detalhes-content');

    // --- Gerenciamento de Modais ---
    
    function abrirModalCadastro() {
        modalBackdrop.classList.remove('hidden');
        modalCadastro.classList.remove('hidden');
    }

    async function abrirModalDetalhes(idLead) {
        modalBackdrop.classList.remove('hidden');
        modalDetalhes.classList.remove('hidden');
        modalDetalhesContent.innerHTML = '<p class="text-center p-8 text-slate-500">Carregando dados...</p>';

        try {
            // RF02 / RF08: Busca dados do lead e interações
            const response = await fetch(`${CONTROLLER_URL}?action=buscar_lead&id_lead=${idLead}`);
            if (!response.ok) throw new Error('Falha na rede ao buscar dados.');
            
            const result = await response.json();
            if (!result.success) throw new Error(result.message);

            // Renderiza o conteúdo do modal
            renderizarDetalhesLead(result.data);

        } catch (error) {
            console.error('Erro ao abrir detalhes:', error);
            modalDetalhesContent.innerHTML = `<p class="text-center p-8 text-red-500">Erro: ${error.message}</p>`;
        }
    }
    
    function fecharModais() {
        modalBackdrop.classList.add('hidden');
        modalCadastro.classList.add('hidden');
        modalDetalhes.classList.add('hidden');
        modalDetalhesContent.innerHTML = ''; // Limpa o conteúdo
    }

    /**
     * RF02/RF06/RF08: Renderiza o modal de detalhes/edição
     */
    function renderizarDetalhesLead(data) {
        const lead = data.dados;
        const interacoes = data.interacoes;
        
        // (RF06) Lista de usuários para atribuição
        const usuariosOptions = (<?= json_encode($usuarios) ?>).map(u => 
            `<option value="${u.id_usuario}" ${u.id_usuario == lead.id_usuario_responsavel ? 'selected' : ''}>
                ${escapeHTML(u.nome)}
            </option>`
        ).join('');

        // (RF08) Histórico de Interações
        let interacoesHtml = interacoes.length === 0 
            ? '<p class="text-sm text-slate-400 text-center py-4">Nenhuma interação registrada.</p>'
            : interacoes.map(i => `
                <div class="p-3 bg-slate-50 rounded-md border border-slate-200">
                    <p class="text-sm text-slate-700">${escapeHTML(i.descricao)}</p>
                    <div class="text-xs text-slate-500 mt-2 flex justify-between">
                        <span>Por: <strong>${escapeHTML(i.nome_usuario)}</strong> (${escapeHTML(i.tipo_interacao)})</span>
                        <span>${new Date(i.data_interacao).toLocaleString('pt-BR')}</span>
                    </div>
                </div>
            `).join('<div class="my-2"></div>');

        
        // HTML Completo do Modal
        modalDetalhesContent.innerHTML = `
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-slate-800">Detalhes do Lead</h2>
                <button onclick="fecharModais()" class="text-slate-400 hover:text-slate-600 text-3xl">&times;</button>
            </div>

            <!-- TABS (Abas) -->
            <div class="border-b border-slate-200 mb-4">
                <nav class="flex gap-4">
                    <button onclick="mudarTab(this, 'tab-editar')" class="tab-link -mb-px border-b-2 border-indigo-500 text-indigo-600 font-semibold py-2 px-1 text-sm">Editar (RF02)</button>
                    <button onclick="mudarTab(this, 'tab-interacoes')" class="tab-link border-b-2 border-transparent text-slate-500 hover:text-slate-700 py-2 px-1 text-sm">Interações (RF08)</button>
                </nav>
            </div>
            
            <!-- CONTEÚDO DAS TABS -->

            <!-- Aba Editar (RF02, RF06) -->
            <div id="tab-editar" class="tab-content space-y-4">
                <form action="../../controllers/LeadController.php" method="POST">
                    <input type="hidden" name="action" value="editar">
                    <input type="hidden" name="id_lead" value="${lead.id_lead}">
                    
                    <div class="space-y-4">
                         <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Nome <span class="text-red-500">*</span></label>
                            <input type="text" name="nome" value="${escapeHTML(lead.nome)}" class="w-full border-slate-300 rounded-lg text-sm" required>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Telefone <span class="text-red-500">*</span></label>
                                <input type="text" name="telefone" value="${escapeHTML(lead.telefone)}" class="w-full border-slate-300 rounded-lg text-sm" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Email</label>
                                <input type="email" name="email" value="${escapeHTML(lead.email)}" class="w-full border-slate-300 rounded-lg text-sm">
                            </div>
                        </div>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Origem</label>
                                <input type="text" name="origem" value="${escapeHTML(lead.origem)}" class="w-full border-slate-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-600 mb-1">Interesse</label>
                                <input type="text" name="interesse" value="${escapeHTML(lead.interesse)}" class="w-full border-slate-300 rounded-lg text-sm">
                            </div>
                        </div>
                        <!-- RF06: Atribuir Responsável (Coordenador) -->
                        <?php if ($usuario_e_coordenador): ?>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Responsável</Sabel>
                            <select name="id_usuario_responsavel" class="w-full border-slate-300 rounded-lg text-sm">
                                ${usuariosOptions}
                            </select>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex justify-between items-center pt-6 mt-6 border-t border-slate-200">
                        <!-- RF03: Excluir Lead -->
                        <button type="button" onclick="excluirLead(${lead.id_lead}, '${escapeHTML(lead.nome)}')" class="px-4 py-2 bg-red-100 text-red-600 rounded-lg text-sm font-semibold hover:bg-red-200 transition-colors">
                            Excluir Lead
                        </button>
                        <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>

            <!-- Aba Interações (RF08) -->
            <div id="tab-interacoes" class="tab-content hidden space-y-4">
                <!-- Formulário de Nova Interação -->
                <form id="form-nova-interacao" class="bg-white p-4 rounded-lg border space-y-3">
                    <h4 class="font-semibold text-slate-700">Registrar Nova Interação</h4>
                    <div>
                        <textarea id="interacao_descricao" class="w-full border-slate-300 rounded-lg text-sm" rows="3" placeholder="Descreva a interação..."></textarea>
                    </div>
                    <div class="flex justify-between items-center">
                        <select id="interacao_tipo" class="border-slate-300 rounded-lg text-sm">
                            <option value="ligacao">Ligação</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="email">Email</option>
                            <option value="visita">Visita</option>
                            <option value="outro">Outro</option>
                        </select>
                        <button type="button" onclick="registrarInteracao(${lead.id_lead})" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold">Registrar</button>
                    </div>
                </form>

                <!-- Histórico -->
                <h4 class="font-semibold text-slate-700 pt-4 border-t">Histórico</h4>
                <div class="max-h-60 overflow-y-auto space-y-3 pr-2">
                    ${interacoesHtml}
                </div>
            </div>
        `;
    }

    function mudarTab(btn, tabId) {
        // Remove classe ativa de todos
        document.querySelectorAll('.tab-link').forEach(link => {
            link.classList.remove('border-indigo-500', 'text-indigo-600', 'font-semibold');
            link.classList.add('border-transparent', 'text-slate-500');
        });
        // Esconde todos os conteúdos
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });

        // Ativa o botão clicado
        btn.classList.add('border-indigo-500', 'text-indigo-600', 'font-semibold');
        btn.classList.remove('border-transparent', 'text-slate-500');
        // Mostra o conteúdo da tab
        document.getElementById(tabId).classList.remove('hidden');
    }

    // --- Lógica de Drag and Drop (RF05) ---

    let leadIdSendoArrastado = null;

    function handleDragStart(event, idLead) {
        leadIdSendoArrastado = idLead;
        event.dataTransfer.effectAllowed = 'move';
        // Adiciona classe de "arrastando" ao card
        event.target.classList.add('dragging');
    }

    function handleDragOver(event) {
        event.preventDefault(); // Necessário para permitir o 'drop'
        event.dataTransfer.dropEffect = 'move';
        
        // Efeito visual na coluna (opcional)
        const coluna = event.currentTarget;
        coluna.classList.add('drag-over');
    }

    document.querySelectorAll('.pipeline-column').forEach(col => {
        col.addEventListener('dragleave', (e) => e.currentTarget.classList.remove('drag-over'));
    });

    async function handleDrop(event, novoStatus) {
        event.preventDefault();
        const colunaDestino = event.currentTarget;
        colunaDestino.classList.remove('drag-over');

        if (!leadIdSendoArrastado) return;

        const idLead = leadIdSendoArrastado;
        const cardSendoArrastado = document.querySelector(`.lead-card[data-id='${idLead}']`);
        const colunaOrigem = cardSendoArrastado.closest('.pipeline-column'); // Coluna original
        
        // Não faz nada se soltar na mesma coluna
        if (colunaDestino.dataset.status === colunaOrigem.dataset.status) {
            cardSendoArrastado.classList.remove('dragging');
            leadIdSendoArrastado = null;
            return;
        }

        // 1. Otimismo UI: Move o card visualmente
        colunaDestino.querySelector('.pipeline-cards').appendChild(cardSendoArrastado);
        cardSendoArrastado.classList.remove('dragging');
        
        // 2. (RF05) Envia a atualização para o backend
        try {
            // ATUALIZADO: Enviando como x-www-form-urlencoded (igual ao seu exemplo)
            const response = await fetch(CONTROLLER_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'mover_pipeline',
                    id_lead: idLead,
                    novo_status: novoStatus
                })
            });
            
            const result = await response.json();

            if (!result.success) {
                // (RF05 Erro): Reverte a ação se falhar
                console.error('Falha ao mover:', result.message);
                showToast(result.message || 'Falha ao mover o lead.', 'error');
                // Reverte o card para a coluna original
                colunaOrigem.querySelector('.pipeline-cards').appendChild(cardSendoArrastado);
            } else {
                 showToast('Lead movido com sucesso!', 'success');
                 // (RF10) Notificação seria gerada aqui
            }

        } catch (error) {
            console.error('Erro de rede:', error);
            showToast('Erro de conexão. Tente novamente.', 'error');
            // Reverte o card
            colunaOrigem.querySelector('.pipeline-cards').appendChild(cardSendoArrastado);
        } finally {
            // ATUALIZA AS CONTAGENS E PLACEHOLDERS DE AMBAS AS COLUNAS
            updateLeadCount(colunaOrigem);
            updateLeadCount(colunaDestino);
            togglePlaceholder(colunaOrigem);
            togglePlaceholder(colunaDestino);

            leadIdSendoArrastado = null;
        }
    }

    /**
     * Atualiza o número no cabeçalho da coluna
     */
    function updateLeadCount(coluna) {
        if (!coluna) return;
        const total = coluna.querySelectorAll('.lead-card').length;
        const countElement = coluna.querySelector('h2 span');
        if (countElement) {
            countElement.textContent = total;
        }
    }

    /**
     * Mostra/esconde a mensagem "Arraste leads para cá"
     */
    function togglePlaceholder(coluna) {
        if (!coluna) return;
        const total = coluna.querySelectorAll('.lead-card').length;
        const cardsContainer = coluna.querySelector('.pipeline-cards');
        let placeholder = cardsContainer.querySelector('.pipeline-placeholder');

        if (total === 0 && !placeholder) {
            const newPlaceholder = document.createElement('div');
            newPlaceholder.className = 'text-center text-slate-400 text-sm p-4 border-2 border-dashed rounded-lg pipeline-placeholder';
            newPlaceholder.textContent = 'Arraste leads para cá.';
            cardsContainer.appendChild(newPlaceholder);
        } else if (total > 0 && placeholder) {
            placeholder.remove();
        }
    }

    /**
     * RF08: Registrar Nova Interação (via Fetch)
     */
    async function registrarInteracao(idLead) {
        const descricao = document.getElementById('interacao_descricao').value;
        const tipo = document.getElementById('interacao_tipo').value;

        // (RF08 Erro)
        if (descricao.trim() === '') {
            showToast('A descrição é obrigatória.', 'error');
            return;
        }

        try {
            // ATUALIZADO: Enviando como x-www-form-urlencoded
             const response = await fetch(CONTROLLER_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'registrar_interacao',
                    id_lead: idLead,
                    descricao: descricao,
                    tipo_interacao: tipo,
                })
            });
            
            const result = await response.json();
            if (result.success) {
                showToast('Interação registrada!', 'success');
                // Atualiza o modal para mostrar a nova interação
                abrirModalDetalhes(idLead); 
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            showToast(`Erro: ${error.message}`, 'error');
        }
    }

    /**
 * RF03: Excluir Lead (via Fetch e SweetAlert)
 */
async function excluirLead(idLead, nomeLead) {
    // 1. Chame o SweetAlert em vez do confirm()
    Swal.fire({
        title: 'Você tem certeza?',
        text: `Deseja realmente excluir (inativar) o lead "${nomeLead}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        // 2. Verifique se o usuário clicou em "Sim, excluir!"
        if (result.isConfirmed) {
            // 3. Coloque toda a lógica de fetch original aqui dentro
            try {
                const response = await fetch(CONTROLLER_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'excluir',
                        id_lead: idLead
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // Mostra sucesso no SweetAlert
                    Swal.fire(
                        'Excluído!',
                        'O lead foi marcado como inativo.',
                        'success'
                    );

                    // Remove o card da UI
                    document.querySelector(`.lead-card[data-id='${idLead}']`).remove();
                    fecharModais();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                // Mostra erro no SweetAlert
                Swal.fire(
                    'Erro!',
                    `Erro: ${error.message}`,
                    'error'
                );
            }
        }
    });
}



    /**
     * RF07: Filtro de Busca (local, em JS)
     */
    document.getElementById('filtroBusca').addEventListener('input', (e) => {
        const termo = e.target.value.toLowerCase();
        document.querySelectorAll('.lead-card').forEach(card => {
            const nome = card.dataset.nome.toLowerCase();
            const email = card.dataset.email.toLowerCase();
            
            if (nome.includes(termo) || email.includes(termo)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });

    /**
     * Lógica de Toast (Notificações de feedback)
     */
    let toastTimeout;
    function showToast(message, type = 'success') {
        // Reusa os toasts da sessão ou cria um novo
        let toast = document.getElementById(type === 'success' ? 'toast-sucesso' : 'toast-erro');
        
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toast-' + type;
            toast.className = `fixed bottom-5 right-5 w-80 p-4 rounded-lg shadow-lg text-white z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            toast.innerHTML = `<p>${message}</p>`;
            document.body.appendChild(toast);
        } else {
            toast.querySelector('p').textContent = message;
            toast.classList.remove('hidden');
        }

        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            toast.classList.add('hidden');
        }, 4000);
    }
    
    // Auto-esconder toasts da sessão
    document.addEventListener('DOMContentLoaded', () => {
        const toastSucesso = document.getElementById('toast-sucesso');
        const toastErro = document.getElementById('toast-erro');
        
        if (toastSucesso) {
            setTimeout(() => toastSucesso.classList.add('hidden'), 4000);
        }
        if (toastErro) {
            setTimeout(() => toastErro.classList.add('hidden'), 4000);
        }
    });

    // Função utilitária
    function escapeHTML(str) {
        if (str === null || str === undefined) return '';
        return str.toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }


</script>


