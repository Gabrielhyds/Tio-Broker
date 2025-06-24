<!-- Estrutura principal da interface de chat, dividida em colunas. -->
<div class="grid grid-cols-1 lg:grid-cols-4 gap-0">
    <!-- Coluna de Usuários (à esquerda) -->
    <div class="lg:col-span-1 bg-white rounded-xl shadow-md p-5 border-r border-gray-200">
        <h2 class="text-xl font-bold mb-4">Usuários</h2>
        <!-- Bloco PHP: Mostra o filtro de imobiliárias apenas para SuperAdmin. -->
        <?php if ($permissao === 'SuperAdmin'): ?>
            <form method="GET" class="mb-4">
                <!-- Select para filtrar usuários por imobiliária. 'onchange' submete o formulário automaticamente. -->
                <select name="id_imobiliaria" onchange="this.form.submit()" class="w-full border rounded-lg px-4 py-2 bg-gray-50">
                    <option value="">-- Filtrar por Imobiliária --</option>
                    <!-- Loop PHP para popular o select com as imobiliárias disponíveis. -->
                    <?php foreach ($listaImobiliarias as $imob): ?>
                        <option value="<?= $imob['id_imobiliaria'] ?>" <?= ($id_imobiliaria_filtro == $imob['id_imobiliaria']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($imob['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        <?php endif; ?>

        <!-- Lista de usuários disponíveis para conversar. -->
        <ul class="divide-y divide-gray-200">
            <!-- Loop para exibir cada usuário. -->
            <?php foreach ($usuariosDisponiveis as $u): ?>
                <!-- Pula a exibição do próprio usuário logado na lista. -->
                <?php if ($u['id_usuario'] == $id_usuario_logado) continue; ?>
                <li>
                    <!-- Link para iniciar/abrir a conversa com o usuário da lista. -->
                    <a href="chat.php?id_destino=<?= $u['id_usuario'] ?>&id_imobiliaria=<?= $id_imobiliaria_filtro ?>" class="block px-4 py-3 hover:bg-gray-100 rounded-lg">
                        <div class="font-semibold text-gray-800"><?= htmlspecialchars($u['nome']) ?></div>
                        <div class="text-sm text-gray-500">
                            <!-- Mostra um trecho da última mensagem ou um texto padrão. -->
                            <?= isset($ultimasMensagens[$u['id_usuario']]) ? htmlspecialchars(substr($ultimasMensagens[$u['id_usuario']]['mensagem'], 0, 30)) . '...' : 'Nenhuma conversa iniciada' ?>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Coluna de Mensagens (à direita) -->
    <div class="lg:col-span-3 bg-white rounded-xl shadow-md p-5">
        <h2 class="text-xl font-bold mb-4">Mensagens</h2>
        <!-- Contêiner onde as mensagens da conversa ativa serão carregadas. -->
        <div id="mensagens" class="h-[calc(100vh-300px)] overflow-y-auto bg-gray-50 rounded p-4 space-y-2 relative overflow-visible">

            <!-- Mensagem padrão exibida se nenhuma conversa estiver ativa. -->
            <?php if (!$id_conversa_ativa): ?>
                <p class="text-center text-gray-400">Selecione um colaborador para iniciar a conversa.</p>
            <?php endif; ?>
        </div>

        <!-- Formulário para enviar mensagem (só aparece se uma conversa estiver ativa). -->
        <?php if ($id_conversa_ativa): ?>
            <form id="form-mensagem" action="../../controllers/ChatController.php" method="POST" class="mt-4 flex items-center gap-3 relative">
                <!-- Campos ocultos com dados necessários para o backend. -->
                <input type="hidden" name="action" value="enviar_mensagem">
                <input type="hidden" name="id_conversa" value="<?= $id_conversa_ativa ?>">
                <input type="hidden" name="id_destino" value="<?= $id_destino ?>">
                <?php if (isset($id_imobiliaria_filtro)): ?>
                    <input type="hidden" name="id_imobiliaria" value="<?= htmlspecialchars($id_imobiliaria_filtro) ?>">
                <?php endif; ?>

                <!-- Campo de texto para digitar a mensagem. -->
                <input type="text" maxlength="1000" name="mensagem" id="mensagem-input" class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Digite sua mensagem..." required autocomplete="off">
                <!-- Botão para abrir o seletor de emojis. -->
                <button type="button" id="emoji-btn" class="text-xl">😊</button>
                <!-- Botão para enviar a mensagem. -->
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">Enviar</button>
                <!-- O componente de seletor de emojis, inicialmente oculto. -->
                <emoji-picker class="light absolute hidden z-50" style="bottom: 100%; right: 0;"></emoji-picker>
            </form>
        <?php endif; ?>
    </div>
</div>
<!-- Seletor de reações, um elemento global que será posicionado via JS. -->
<div class="seletor-reacao fixed bg-white rounded-lg shadow-lg p-2 flex gap-2 hidden z-50" id="seletor-reacao-global">
    <span class="emoji-reacao text-xl cursor-pointer" data-reacao="👍">👍</span>
    <span class="emoji-reacao text-xl cursor-pointer" data-reacao="❤️">❤️</span>
    <span class="emoji-reacao text-xl cursor-pointer" data-reacao="😂">😂</span>
    <span class="emoji-reacao text-xl cursor-pointer" data-reacao="😮">😮</span>
    <span class="emoji-reacao text-xl cursor-pointer" data-reacao="😢">😢</span>
    <span class="emoji-reacao text-xl cursor-pointer" data-reacao="🙏">🙏</span>
</div>
<!-- Importa o módulo do componente de seletor de emojis. -->
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
<script>
    // Variável para armazenar o intervalo de atualização (polling).
    let pollingInterval;
    // Referência ao seletor de reações global.
    const seletorReacao = document.getElementById('seletor-reacao-global');

    // Função para carregar as mensagens da conversa ativa.
    function carregarMensagens() {
        const idConversa = <?= json_encode($id_conversa_ativa ?? null) ?>; // Pega o ID da conversa do PHP.
        if (!idConversa) return; // Não faz nada se não houver conversa ativa.
        // Busca o conteúdo HTML das mensagens no backend. `Date.now()` evita cache.
        fetch(`get_mensagens.php?id_conversa=${idConversa}&t=${Date.now()}`)
            .then(res => res.text()) // Converte a resposta para texto.
            .then(html => {
                const mensagensBox = document.getElementById('mensagens');
                // Verifica se o usuário está com a rolagem no final da caixa de mensagens.
                const isScrolledToBottom = mensagensBox.scrollTop + mensagensBox.clientHeight >= mensagensBox.scrollHeight - 30;
                // Atualiza o conteúdo da caixa de mensagens.
                mensagensBox.innerHTML = html;
                // Se o usuário estava no final, mantém a rolagem no final após carregar novas mensagens.
                if (isScrolledToBottom) mensagensBox.scrollTop = mensagensBox.scrollHeight;
            });
    }

    // Função para posicionar e exibir o seletor de reações. (Não está sendo usada na lógica atual, mas está definida).
    function mostrarSeletorReacao(btn) {
        // ... (lógica de posicionamento omitida para brevidade)
    }

    // Função para esconder o seletor de reações.
    function esconderSeletorReacao() {
        seletorReacao.classList.add('hidden');
    }

    // Função para enviar uma reação ao backend.
    function enviarReacao(idMensagem, reacao) {
        const formData = new FormData();
        formData.append('action', 'reagir_mensagem');
        formData.append('id_mensagem', idMensagem);
        formData.append('reacao', reacao);

        fetch('../../controllers/ChatController.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                // Se a reação foi enviada com sucesso, recarrega as mensagens para exibir a atualização.
                if (data.success) carregarMensagens();
            })
            .catch(console.error);

        esconderSeletorReacao(); // Esconde o seletor após a ação.
    }

    // Executa quando o DOM estiver totalmente carregado.
    document.addEventListener('DOMContentLoaded', () => {
        const idConversa = <?= json_encode($id_conversa_ativa ?? null) ?>;
        // Se uma conversa estiver ativa, carrega as mensagens imediatamente e inicia o polling.
        if (idConversa) {
            carregarMensagens();
            pollingInterval = setInterval(carregarMensagens, 3000); // Atualiza a cada 3 segundos.
        }

        // Adiciona um ouvinte de eventos global no corpo do documento para gerenciar cliques.
        document.body.addEventListener('click', e => {
            // Lógica para abrir o menu de opções da mensagem (3 pontinhos).
            const btnOpcoes = e.target.closest('.btn-opcoes');
            if (btnOpcoes) {
                e.stopPropagation(); // Impede que o clique se propague para outros elementos.
                document.querySelectorAll('.dropdown-opcoes').forEach(d => d.classList.add('hidden')); // Esconde outros menus.
                const dropdown = btnOpcoes.nextElementSibling;
                if (dropdown) dropdown.classList.toggle('hidden'); // Mostra/esconde o menu clicado.
                return;
            }

            // Lógica para mostrar o seletor de reações ao clicar no botão de reagir.
            const btnReagir = e.target.closest('.btn-reagir');
            if (btnReagir) {
                e.stopPropagation();
                const idMensagem = btnReagir.dataset.idMensagem;
                seletorReacao.dataset.idMensagem = idMensagem; // Armazena o ID da mensagem no seletor.

                // Lógica de posicionamento do seletor de reações.
                const rect = btnReagir.getBoundingClientRect();
                let top = rect.bottom + 5;
                let left = rect.left + rect.width / 2 - seletorReacao.offsetWidth / 2;
                if (left < 5) left = 5;
                if (left + seletorReacao.offsetWidth > window.innerWidth) left = window.innerWidth - seletorReacao.offsetWidth - 5;
                seletorReacao.style.top = `${top}px`;
                seletorReacao.style.left = `${left}px`;
                seletorReacao.classList.remove('hidden');
                return;
            }

            // Lógica para enviar uma reação quando um emoji do seletor é clicado.
            const emoji = e.target.closest('.emoji-reacao');
            if (emoji) {
                const id = seletorReacao.dataset.idMensagem;
                enviarReacao(id, emoji.dataset.reacao);
                return;
            }

            // Se o clique for fora de qualquer elemento interativo, esconde os menus e seletores.
            esconderSeletorReacao();
            document.querySelectorAll('.dropdown-opcoes').forEach(d => d.classList.add('hidden'));

            // Lógica para fechar o seletor de emojis principal.
            const emojiBtn = document.getElementById('emoji-btn');
            const emojiPicker = document.querySelector('emoji-picker');
            if (emojiBtn && emojiPicker) {
                if (!emojiBtn.contains(e.target) && !emojiPicker.contains(e.target)) {
                    emojiPicker.classList.add('hidden');
                }
            }
        });

        // Lógica para o seletor de emojis do campo de input.
        const emojiBtn = document.getElementById('emoji-btn');
        const emojiPicker = document.querySelector('emoji-picker');
        const input = document.getElementById('mensagem-input');

        if (emojiBtn && emojiPicker) {
            // Mostra/esconde o seletor de emojis ao clicar no botão.
            emojiBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                emojiPicker.classList.toggle('hidden');
            });

            // Fecha o seletor se o clique for fora dele.
            document.body.addEventListener('click', (e) => {
                if (!emojiBtn.contains(e.target) && !emojiPicker.contains(e.target)) {
                    emojiPicker.classList.add('hidden');
                }
            });

            // Insere o emoji no campo de texto quando um é clicado.
            emojiPicker.addEventListener('emoji-click', (e) => {
                input.value += e.detail.unicode;
                input.focus(); // Devolve o foco ao campo de texto.
            });
        }
    });
</script>