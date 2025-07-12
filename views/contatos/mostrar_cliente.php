<?php
// As variáveis $cliente, $interacoes e $documentos são fornecidas pelo ClienteController.
if (!isset($cliente) || empty($cliente)) {
    echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg' role='alert'>Não foi possível carregar os dados do cliente.</div>";
    return;
}

function formatarData($data) {
    return $data ? date('d/m/Y \à\s H:i', strtotime($data)) : 'Não informada';
}

function formatarMoeda($valor) {
    return $valor !== null ? 'R$ ' . number_format((float)$valor, 2, ',', '.') : 'Não informado';
}
?>

<!-- Container principal com espaçamento -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Cabeçalho da página com botões de ação -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detalhes do Cliente</h1>
            <p class="text-sm text-gray-500">Visualize e gerencie as informações do cliente.</p>
        </div>
        <div class="flex items-center space-x-2">
            <!-- CORREÇÃO FINAL: Usando BASE_URL para garantir o caminho absoluto -->
            <a href="<?= BASE_URL ?>views/contatos/index.php?controller=cliente&action=listar" class="bg-white hover:bg-gray-100 text-gray-700 text-sm font-medium py-2 px-4 border border-gray-300 rounded-lg shadow-sm flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Voltar
            </a>
            <a href="<?= BASE_URL ?>views/contatos/index.php?controller=cliente&action=editar&id_cliente=<?= htmlspecialchars($cliente['id_cliente']) ?>" class="bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-medium py-2 px-4 rounded-lg shadow-sm flex items-center">
                <i class="fas fa-pencil-alt mr-2"></i> Editar
            </a>
        </div>
    </div>

    <!-- Container principal do perfil -->
    <div class="bg-white shadow-lg rounded-2xl p-6 md:p-8">
        <!-- Header do Perfil -->
        <div class="flex flex-col sm:flex-row items-center border-b border-gray-200 pb-6 mb-6">
            <?php if (!empty($cliente['foto'])): ?>
                <img src="<?= BASE_URL . htmlspecialchars($cliente['foto']) ?>" alt="Foto de <?= htmlspecialchars($cliente['nome']) ?>" class="w-24 h-24 rounded-full object-cover mr-0 sm:mr-6 mb-4 sm:mb-0 border-4 border-gray-100" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-4xl mr-0 sm:mr-6 mb-4 sm:mb-0"><i class="fas fa-user"></i></div>
            <?php else: ?>
                <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-4xl mr-0 sm:mr-6 mb-4 sm:mb-0"><i class="fas fa-user"></i></div>
            <?php endif; ?>
            <div class="text-center sm:text-left">
                <h2 class="text-3xl font-bold text-gray-900"><?= htmlspecialchars($cliente['nome']) ?></h2>
                <p class="text-sm text-gray-500">ID do Cliente: #<?= htmlspecialchars($cliente['id_cliente']) ?></p>
                <?php
                    $isPotencial = ($cliente['tipo_lista'] ?? '') === 'Potencial';
                    $badgeClass = $isPotencial ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                    $iconClass = $isPotencial ? 'fa-check-circle' : 'fa-times-circle';
                ?>
                <span class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold <?= $badgeClass ?>">
                    <i class="fas <?= $iconClass ?> mr-2"></i>
                    <?= htmlspecialchars($cliente['tipo_lista'] ?? 'Não definido') ?>
                </span>
            </div>
        </div>

        <!-- Alertas de Mensagens -->
        <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6" role="alert">
                <p><?= htmlspecialchars($_SESSION['mensagem_sucesso']); ?></p>
            </div>
            <?php unset($_SESSION['mensagem_sucesso']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['mensagem_erro'])): ?>
             <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6" role="alert">
                <p><?= htmlspecialchars($_SESSION['mensagem_erro']); ?></p>
            </div>
            <?php unset($_SESSION['mensagem_erro']); ?>
        <?php endif; ?>

        <!-- Grid de Informações -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Colunas de Informações -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 flex items-center"><i class="fas fa-user-circle mr-3 text-gray-500"></i>Informações Pessoais</h3>
                <div class="flex justify-between text-sm"><strong class="font-medium text-gray-600">Telefone:</strong> <span class="text-gray-800"><?= htmlspecialchars($cliente['numero']) ?></span></div>
                <div class="flex justify-between text-sm"><strong class="font-medium text-gray-600">CPF:</strong> <span class="text-gray-800"><?= htmlspecialchars($cliente['cpf']) ?></span></div>
                <div class="flex justify-between text-sm"><strong class="font-medium text-gray-600">Empreendimento:</strong> <span class="text-gray-800"><?= htmlspecialchars($cliente['empreendimento'] ?? 'Não informado') ?></span></div>
                <div class="flex justify-between text-sm"><strong class="font-medium text-gray-600">Cadastro:</strong> <span class="text-gray-800"><?= formatarData($cliente['criado_em']) ?></span></div>
            </div>
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 flex items-center"><i class="fas fa-dollar-sign mr-3 text-gray-500"></i>Informações Financeiras</h3>
                <div class="flex justify-between text-sm"><strong class="font-medium text-gray-600">Renda:</strong> <span class="text-gray-800"><?= formatarMoeda($cliente['renda']) ?></span></div>
                <div class="flex justify-between text-sm"><strong class="font-medium text-gray-600">Entrada:</strong> <span class="text-gray-800"><?= formatarMoeda($cliente['entrada']) ?></span></div>
                <div class="flex justify-between text-sm"><strong class="font-medium text-gray-600">FGTS:</strong> <span class="text-gray-800"><?= formatarMoeda($cliente['fgts']) ?></span></div>
                <div class="flex justify-between text-sm"><strong class="font-medium text-gray-600">Subsídio:</strong> <span class="text-gray-800"><?= formatarMoeda($cliente['subsidio']) ?></span></div>
            </div>
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 flex items-center"><i class="fas fa-user-tie mr-3 text-gray-500"></i>Informações do Corretor</h3>
                <div class="flex justify-between text-sm"><strong class="font-medium text-gray-600">Corretor:</strong> <span class="text-gray-800"><?= htmlspecialchars($cliente['nome_corretor'] ?? 'Não associado') ?></span></div>
                <div class="flex justify-between text-sm"><strong class="font-medium text-gray-600">Email:</strong> <span class="text-gray-800"><?= htmlspecialchars($cliente['email_corretor'] ?? 'Não informado') ?></span></div>
                <div class="flex justify-between text-sm"><strong class="font-medium text-gray-600">Telefone:</strong> <span class="text-gray-800"><?= htmlspecialchars($cliente['telefone_corretor'] ?? 'Não informado') ?></span></div>
                <div class="flex justify-between text-sm"><strong class="font-medium text-gray-600">Imobiliária:</strong> <span class="text-gray-800"><?= htmlspecialchars($cliente['nome_imobiliaria'] ?? 'Não associada') ?></span></div>
            </div>
        </div>

        <!-- Seção de Ações com Abas (Tabs) -->
        <div class="bg-gray-50 rounded-xl p-6 mb-8 border border-gray-200">
            <div class="mb-4 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg" id="interacao-tab" data-tabs-target="#interacao" type="button" role="tab" aria-controls="interacao" aria-selected="false">
                            <i class="fas fa-comment-dots mr-2"></i>Registrar Interação
                        </button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="documento-tab" data-tabs-target="#documento" type="button" role="tab" aria-controls="documento" aria-selected="false">
                           <i class="fas fa-file-upload mr-2"></i>Anexar Documento
                        </button>
                    </li>
                </ul>
            </div>
            <div id="myTabContent">
                <!-- Formulário de Interação -->
                <div class="hidden p-4 rounded-lg bg-gray-50" id="interacao" role="tabpanel" aria-labelledby="interacao-tab">
                    <!-- CORREÇÃO FINAL: Usando BASE_URL para garantir o caminho absoluto -->
                    <form method="POST" action="<?= BASE_URL ?>views/contatos/index.php?controller=interacao&action=adicionar">
                        <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($cliente['id_cliente']) ?>">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <label for="tipo_interacao" class="block text-sm font-medium text-gray-700">Tipo de Interação</label>
                            <select name="tipo_interacao" id="tipo_interacao" class="col-span-2 mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                <option value="mensagem" selected>Mensagem</option>
                                <option value="telefone">Ligação Telefônica</option>
                                <option value="reuniao">Reunião</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
                            <textarea name="descricao" id="descricao" rows="3" class="col-span-2 mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required placeholder="Digite os detalhes da interação..."></textarea>
                        </div>
                        <div class="text-right mt-4">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Adicionar ao Histórico
                            </button>
                        </div>
                    </form>
                </div>
                <!-- Formulário de Documento -->
                <div class="hidden p-4 rounded-lg bg-gray-50" id="documento" role="tabpanel" aria-labelledby="documento-tab">
                     <!-- CORREÇÃO FINAL: Usando BASE_URL para garantir o caminho absoluto -->
                     <form method="POST" action="<?= BASE_URL ?>views/contatos/index.php?controller=documento&action=adicionar" enctype="multipart/form-data">
                        <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($cliente['id_cliente']) ?>">
                        <div class="space-y-4">
                             <div>
                                <label for="doc_nome_documento" class="block text-sm font-medium text-gray-700">Nome do Documento <span class="text-red-500">*</span></label>
                                <input type="text" name="nome_documento" id="doc_nome_documento" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required placeholder="Ex: Contrato Assinado, RG Frente">
                            </div>
                             <div>
                                <label for="doc_tipo_documento" class="block text-sm font-medium text-gray-700">Tipo do Documento <span class="text-red-500">*</span></label>
                                <input type="text" name="tipo_documento" id="doc_tipo_documento" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required placeholder="Ex: PDF, Contrato, Identidade">
                            </div>
                            <div>
                                <label for="doc_arquivo" class="block text-sm font-medium text-gray-700">Arquivo <span class="text-red-500">*</span></label>
                                <input type="file" name="arquivo_documento" id="doc_arquivo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                                <p class="mt-1 text-xs text-gray-500">Max: 10MB. Tipos: PDF, DOC, DOCX, JPG, PNG.</p>
                            </div>
                        </div>
                        <div class="text-right mt-4">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Anexar Documento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Histórico e Documentos (sem alterações) -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center"><i class="fas fa-history mr-3 text-gray-500"></i>Histórico de Interações</h3>
            <div class="space-y-4">
                <?php if (empty($interacoes)): ?>
                    <div class="text-center text-gray-500 py-6 bg-gray-50 rounded-lg">
                        <i class="fas fa-comment-slash text-3xl text-gray-400 mb-2"></i>
                        <p>Nenhuma interação registrada.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($interacoes as $interacao): 
                        $style = [
                            'mensagem' => ['icon' => 'fas fa-comment-dots', 'color' => 'blue'],
                            'telefone' => ['icon' => 'fas fa-phone-alt', 'color' => 'purple'],
                            'reuniao'  => ['icon' => 'fas fa-users', 'color' => 'green'],
                        ];
                        $tipo = $interacao['tipo_interacao'] ?? 'mensagem';
                        $s = $style[$tipo];
                    ?>
                    <div class="bg-white p-4 rounded-lg border-l-4 border-<?= $s['color'] ?>-500 shadow-sm transition hover:shadow-md">
                        <div class="flex justify-between items-center text-sm mb-2">
                            <div class="flex items-center font-semibold text-<?= $s['color'] ?>-700">
                                <i class="<?= $s['icon'] ?> mr-2"></i>
                                <span><?= htmlspecialchars(ucfirst($tipo)) ?> por <strong><?= htmlspecialchars($interacao['nome_usuario'] ?? 'N/A') ?></strong></span>
                            </div>
                            <span class="text-gray-500"><?= formatarData($interacao['data_interacao']) ?></span>
                        </div>
                        <p class="text-gray-700 text-sm pl-5"><?= nl2br(htmlspecialchars($interacao['descricao'])) ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center"><i class="fas fa-folder-open mr-3 text-gray-500"></i>Documentos Anexados</h3>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <ul class="divide-y divide-gray-200">
                    <?php if (empty($documentos)): ?>
                        <li class="p-6 text-center text-gray-500">
                            <i class="fas fa-file-excel text-3xl text-gray-400 mb-2"></i>
                            <p>Nenhum documento anexado.</p>
                        </li>
                    <?php else: ?>
                        <?php foreach ($documentos as $documento): ?>
                        <li class="p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center hover:bg-gray-50">
                            <div class="flex items-center mb-2 sm:mb-0">
                                <i class="fas fa-file-alt text-blue-500 text-xl mr-4"></i>
                                <div>
                                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($documento['nome_documento']) ?></p>
                                    <p class="text-xs text-gray-500">
                                        Upload por <?= htmlspecialchars($documento['nome_usuario_upload'] ?? 'N/A') ?> em <?= formatarData($documento['data_upload']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex space-x-3 self-end sm:self-center">
                                <a href="<?= BASE_URL ?>views/contatos/index.php?controller=documento&action=baixar&id_documento=<?= htmlspecialchars($documento['id_documento']) ?>" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Ver/Baixar</a>
                                <a href="<?= BASE_URL ?>views/contatos/index.php?controller=documento&action=excluir&id_documento=<?= $documento['id_documento'] ?>&id_cliente=<?= $cliente['id_cliente'] ?>" class="text-red-600 hover:text-red-800 text-sm font-medium" onclick="return confirm('Tem certeza que deseja excluir este documento?')">Excluir</a>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- O JavaScript para controlar as abas (sem alterações) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = [];
    const tabContents = {};

    document.querySelectorAll('[data-tabs-toggle]').forEach(container => {
        const tabContainer = container;
        const contentContainer = document.querySelector(tabContainer.dataset.tabsToggle);

        tabContainer.querySelectorAll('[role="tab"]').forEach(tab => {
            const targetId = tab.dataset.tabsTarget;
            const targetContent = contentContainer.querySelector(targetId);
            
            tabs.push(tab);
            tabContents[targetId] = targetContent;

            tab.addEventListener('click', (e) => {
                e.preventDefault();
                tabs.forEach(t => {
                    t.setAttribute('aria-selected', 'false');
                    t.classList.remove('border-blue-600', 'text-blue-600');
                    t.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300');
                });
                Object.values(tabContents).forEach(content => {
                    content.classList.add('hidden');
                });

                tab.setAttribute('aria-selected', 'true');
                tab.classList.add('border-blue-600', 'text-blue-600');
                tab.classList.remove('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300');
                
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }
            });
        });
        
        if (tabs.length > 0) {
            tabs[0].click();
        }
    });
});
</script>
