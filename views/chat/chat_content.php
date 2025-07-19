<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: views/chat/chat_content.php (VERSÃO FINAL)
|--------------------------------------------------------------------------
| Adicionada a classe 'translating' para evitar o "pisca-pisca" do idioma.
| O script foi simplificado para carregar apenas as traduções do módulo 'chat',
| pois o template_base.php já cuida da tradução do layout.
*/
?>
<!-- Estrutura principal da interface de chat -->
<div class="grid grid-cols-1 lg:grid-cols-4 gap-0">
    <!-- Coluna de Usuários -->
    <div class="lg:col-span-1 bg-white rounded-xl shadow-md p-5 border-r border-gray-200">
        <h2 class="text-xl font-bold mb-4 translating" data-i18n="users.title">Usuários</h2>
        <?php if ($permissao === 'SuperAdmin'): ?>
            <form method="GET" class="mb-4">
                <select name="id_imobiliaria" onchange="this.form.submit()" class="w-full border rounded-lg px-4 py-2 bg-gray-50">
                    <option value="" class="translating" data-i18n="users.filterPlaceholder">-- Filtrar por Imobiliária --</option>
                    <?php foreach ($listaImobiliarias as $imob): ?>
                        <option value="<?= $imob['id_imobiliaria'] ?>" <?= ($id_imobiliaria_filtro == $imob['id_imobiliaria']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($imob['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        <?php endif; ?>

        <ul id="lista-usuarios" class="divide-y divide-gray-200">
            <?php foreach ($usuariosDisponiveis as $u): ?>
                <?php if ($u['id_usuario'] == $id_usuario_logado) continue; ?>
                <li>
                    <a href="chat.php?id_destino=<?= $u['id_usuario'] ?>&id_imobiliaria=<?= $id_imobiliaria_filtro ?>" class="block px-4 py-3 hover:bg-gray-100 rounded-lg">
                        <div class="font-semibold text-gray-800"><?= htmlspecialchars($u['nome']) ?></div>
                        <div class="text-sm text-gray-500" data-user-id="<?= $u['id_usuario'] ?>">
                            <?= isset($ultimasMensagens[$u['id_usuario']]) ? htmlspecialchars(substr($ultimasMensagens[$u['id_usuario']]['mensagem'], 0, 30)) . '...' : '<span class="translating" data-i18n="users.noConversation">Nenhuma conversa iniciada</span>' ?>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Coluna de Mensagens -->
    <div class="lg:col-span-3 bg-white rounded-xl shadow-md p-5">
        <h2 class="text-xl font-bold mb-4 translating" data-i18n="messages.title">Mensagens</h2>
        <div id="mensagens" class="h-[calc(100vh-300px)] overflow-y-auto bg-gray-50 rounded p-4 space-y-2 relative overflow-visible">
            <?php if (!$id_conversa_ativa): ?>
                <p class="text-center text-gray-400 translating" data-i18n="messages.startConversation">Selecione um colaborador para iniciar a conversa.</p>
            <?php endif; ?>
        </div>

        <?php if ($id_conversa_ativa): ?>
            <form id="form-mensagem" action="../../controllers/ChatController.php" method="POST" class="mt-4 flex items-center gap-3 relative">
                <input type="hidden" name="action" value="enviar_mensagem">
                <input type="hidden" name="id_conversa" value="<?= $id_conversa_ativa ?>">
                <input type="hidden" name="id_destino" value="<?= $id_destino ?>">
                <?php if (isset($id_imobiliaria_filtro)): ?>
                    <input type="hidden" name="id_imobiliaria" value="<?= htmlspecialchars($id_imobiliaria_filtro) ?>">
                <?php endif; ?>

                <input type="text" maxlength="1000" name="mensagem" id="mensagem-input" class="flex-1 border border-gray-300 rounded-lg px-4 py-2" data-i18n-placeholder="messages.placeholder" placeholder="Digite sua mensagem..." required autocomplete="off">
                <button type="button" id="emoji-btn" class="text-xl">😊</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg translating" data-i18n="messages.sendButton">Enviar</button>
                <emoji-picker class="light absolute hidden z-50" style="bottom: 100%; right: 0;"></emoji-picker>
            </form>
        <?php endif; ?>
    </div>
</div>
<div class="seletor-reacao fixed bg-white rounded-lg shadow-lg p-2 flex gap-2 hidden z-50" id="seletor-reacao-global">
    <span class="emoji-reacao text-xl cursor-pointer" data-reacao="👍">👍</span>
    <span class="emoji-reacao text-xl cursor-pointer" data-reacao="❤️">❤️</span>
    <span class="emoji-reacao text-xl cursor-pointer" data-reacao="😂">😂</span>
    <span class="emoji-reacao text-xl cursor-pointer" data-reacao="😮">😮</span>
    <span class="emoji-reacao text-xl cursor-pointer" data-reacao="😢">😢</span>
    <span class="emoji-reacao text-xl cursor-pointer" data-reacao="🙏">🙏</span>
</div>
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let translations = {};
        let currentLang = 'pt-br';

        function t(key) {
            return key.split('.').reduce((obj, i) => obj && obj[i], translations) || key;
        }

        function applyTranslations() {
            document.querySelectorAll('[data-i18n]').forEach(el => {
                // Não traduz novamente os elementos da sidebar, pois o template_base já fez isso.
                if (!el.closest('#sidebar')) {
                    const key = el.dataset.i18n;
                    const translation = t(key);
                    if (translation !== key) {
                        el.innerText = translation;
                    }
                }
                el.classList.remove('translating');
            });
            document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
                el.placeholder = t(el.dataset.i18nPlaceholder);
            });
        }

        async function loadChatTranslations(lang) {
            try {
                const response = await fetch(`../../controllers/TraducaoController.php?modulo=chat&lang=${lang}`);
                const result = await response.json();
                if (result.success) {
                    translations = result.data;
                    applyTranslations();
                }
            } catch (error) {
                console.error('Failed to load chat translations:', error);
            }
        }

        let pollingInterval;
        const seletorReacao = document.getElementById('seletor-reacao-global');

        function carregarMensagens() {
            const idConversa = <?= json_encode($id_conversa_ativa ?? null) ?>;
            if (!idConversa) return;
            fetch(`get_mensagens.php?id_conversa=${idConversa}&lang=${currentLang}&t=${Date.now()}`)
                .then(res => res.text())
                .then(html => {
                    const mensagensBox = document.getElementById('mensagens');
                    const isScrolledToBottom = mensagensBox.scrollTop + mensagensBox.clientHeight >= mensagensBox.scrollHeight - 30;
                    mensagensBox.innerHTML = html;
                    if (isScrolledToBottom) mensagensBox.scrollTop = mensagensBox.scrollHeight;
                });
        }

        function esconderSeletorReacao() {
            seletorReacao.classList.add('hidden');
        }

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
                    if (data.success) carregarMensagens();
                })
                .catch(console.error);
            esconderSeletorReacao();
        }

        async function initializeChat() {
            currentLang = "<?= $_SESSION['usuario']['configuracoes']['language'] ?? '' ?>" || localStorage.getItem('calendarLang') || 'pt-br';

            await loadChatTranslations(currentLang);

            const idConversa = <?= json_encode($id_conversa_ativa ?? null) ?>;
            if (idConversa) {
                carregarMensagens();
                pollingInterval = setInterval(carregarMensagens, 3000);
            }
        }

        initializeChat();

        document.body.addEventListener('click', e => {
            const btnOpcoes = e.target.closest('.btn-opcoes');
            if (btnOpcoes) {
                e.stopPropagation();
                document.querySelectorAll('.dropdown-opcoes').forEach(d => d.classList.add('hidden'));
                const dropdown = btnOpcoes.nextElementSibling;
                if (dropdown) dropdown.classList.toggle('hidden');
                return;
            }
            const btnReagir = e.target.closest('.btn-reagir');
            if (btnReagir) {
                e.stopPropagation();
                const idMensagem = btnReagir.dataset.idMensagem;
                seletorReacao.dataset.idMensagem = idMensagem;
                const rect = btnReagir.getBoundingClientRect();
                seletorReacao.style.top = `${rect.bottom + 5}px`;
                seletorReacao.style.left = `${rect.left + rect.width / 2 - seletorReacao.offsetWidth / 2}px`;
                seletorReacao.classList.remove('hidden');
                return;
            }
            const emoji = e.target.closest('.emoji-reacao');
            if (emoji) {
                const id = seletorReacao.dataset.idMensagem;
                enviarReacao(id, emoji.dataset.reacao);
                return;
            }
            esconderSeletorReacao();
            document.querySelectorAll('.dropdown-opcoes').forEach(d => d.classList.add('hidden'));
            const emojiBtn = document.getElementById('emoji-btn');
            const emojiPicker = document.querySelector('emoji-picker');
            if (emojiBtn && emojiPicker && !emojiBtn.contains(e.target) && !emojiPicker.contains(e.target)) {
                emojiPicker.classList.add('hidden');
            }
        });

        const emojiBtn = document.getElementById('emoji-btn');
        const emojiPicker = document.querySelector('emoji-picker');
        const input = document.getElementById('mensagem-input');
        if (emojiBtn && emojiPicker) {
            emojiBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                emojiPicker.classList.toggle('hidden');
            });
            emojiPicker.addEventListener('emoji-click', (e) => {
                input.value += e.detail.unicode;
                input.focus();
            });
        }
    });
</script>