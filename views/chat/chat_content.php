<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: views/chat/chat_content.php (VERSÃO COM TÍTULO DINÂMICO)
|--------------------------------------------------------------------------
| - O título da coluna de mensagens agora exibe o nome do destinatário.
| - Mantém as melhorias anteriores de avatares na lista de usuários.
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
                <li data-user-id="<?= $u['id_usuario'] ?>">
                    <a href="chat.php?id_destino=<?= $u['id_usuario'] ?>&id_imobiliaria=<?= $id_imobiliaria_filtro ?>" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-100 rounded-lg">
                        <div class="w-10 h-10 flex-shrink-0">
                            <?php
                            $avatar_url = '';
                            if (!empty($u['foto'])) {
                                $avatar_url = '../../public/uploads/profile_photos/' . htmlspecialchars($u['foto']);
                            } else {
                                $inicial = mb_strtoupper(mb_substr($u['nome'], 0, 1));
                                $avatar_url = "https://placehold.co/100x100/7c3aed/ffffff?text={$inicial}";
                            }
                            ?>
                            <img src="<?= $avatar_url ?>" class="w-full h-full rounded-full object-cover" alt="Avatar de <?= htmlspecialchars($u['nome']) ?>">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-center">
                                <div class="font-semibold text-gray-800 truncate"><?= htmlspecialchars($u['nome']) ?></div>
                                <span class="bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden" data-unread-count-for="<?= $u['id_usuario'] ?>"></span>
                            </div>
                            <div class="text-sm text-gray-500 truncate" data-last-message-for="<?= $u['id_usuario'] ?>">
                                <?= isset($ultimasMensagens[$u['id_usuario']]) ? htmlspecialchars($ultimasMensagens[$u['id_usuario']]['mensagem']) : '<span class="translating" data-i18n="users.noConversation">Nenhuma conversa iniciada</span>' ?>
                            </div>
                            <input type="hidden" class="timestamp" value="<?= isset($ultimasMensagens[$u['id_usuario']]) ? strtotime($ultimasMensagens[$u['id_usuario']]['data_envio']) : 0 ?>">
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Coluna de Mensagens -->
    <div class="lg:col-span-3 bg-white rounded-xl shadow-md p-5">
        <!-- **MELHORIA**: O título agora é dinâmico. -->
        <h2 class="text-xl font-bold mb-4">
            <?php
            if (isset($nome_destino) && $id_conversa_ativa) {
                echo htmlspecialchars($nome_destino);
            } else {
                // Fallback para o título genérico se nenhuma conversa estiver ativa.
                echo '<span class="translating" data-i18n="messages.title">Mensagens</span>';
            }
            ?>
        </h2>
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

        function updateUserList() {
            fetch('get_chat_updates.php?t=' + Date.now())
                .then(res => res.json())
                .then(data => {
                    const userList = document.getElementById('lista-usuarios');
                    if (!userList) return;

                    const userItems = Array.from(userList.querySelectorAll('li'));
                    let needsReordering = false;

                    userItems.forEach(item => {
                        const userId = item.dataset.userId;
                        if (!userId) return;
                        
                        const lastMessageEl = item.querySelector(`[data-last-message-for="${userId}"]`);
                        const unreadCountEl = item.querySelector(`[data-unread-count-for="${userId}"]`);
                        const timestampInput = item.querySelector('.timestamp');

                        if (data[userId]) {
                            const update = data[userId];
                            const currentTimestamp = parseInt(timestampInput.value, 10);

                            if (update.data_envio > currentTimestamp) {
                                needsReordering = true;
                                timestampInput.value = update.data_envio;
                                if (lastMessageEl) {
                                    lastMessageEl.textContent = update.mensagem.substring(0, 30) + (update.mensagem.length > 30 ? '...' : '');
                                }
                            }
                            
                            if (unreadCountEl) {
                                if (update.total_nao_lidas > 0) {
                                    unreadCountEl.textContent = update.total_nao_lidas;
                                    unreadCountEl.classList.remove('hidden');
                                } else {
                                    unreadCountEl.classList.add('hidden');
                                }
                            }
                        }
                    });

                    if (needsReordering) {
                        const sortedItems = userItems.sort((a, b) => {
                            const timeA = parseInt(a.querySelector('.timestamp').value, 10);
                            const timeB = parseInt(b.querySelector('.timestamp').value, 10);
                            return timeB - timeA;
                        });
                        sortedItems.forEach(item => userList.appendChild(item));
                    }
                })
                .catch(console.error);
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
            
            setInterval(updateUserList, 5000);
        }

        initializeChat();

        document.body.addEventListener('click', e => {
            const btnOpcoes = e.target.closest('.btn-opcoes');
            if (btnOpcoes) {
                e.stopPropagation();
                const allDropdowns = document.querySelectorAll('.dropdown-opcoes');
                const currentDropdown = btnOpcoes.nextElementSibling;
                allDropdowns.forEach(d => {
                    if (d !== currentDropdown) {
                        d.classList.add('hidden');
                    }
                });
                if (currentDropdown) currentDropdown.classList.toggle('hidden');
                return;
            }
            
            const btnReagir = e.target.closest('.btn-reagir');
            if (btnReagir) {
                e.stopPropagation();
                const idMensagem = btnReagir.dataset.idMensagem;
                seletorReacao.dataset.idMensagem = idMensagem;
                const rect = btnReagir.getBoundingClientRect();
                seletorReacao.style.top = `${window.scrollY + rect.bottom + 5}px`;
                seletorReacao.style.left = `${window.scrollX + rect.left + rect.width / 2 - seletorReacao.offsetWidth / 2}px`;
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
