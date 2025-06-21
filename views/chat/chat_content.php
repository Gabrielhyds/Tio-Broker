<div class="grid grid-cols-1 lg:grid-cols-4 gap-0">
    <!-- Usuários -->
    <div class="lg:col-span-1 bg-white rounded-xl shadow-md p-5 border-r border-gray-200">
        <h2 class="text-xl font-bold mb-4">Usuários</h2>
        <?php if ($permissao === 'SuperAdmin'): ?>
            <form method="GET" class="mb-4">
                <select name="id_imobiliaria" onchange="this.form.submit()" class="w-full border rounded-lg px-4 py-2 bg-gray-50">
                    <option value="">-- Filtrar por Imobiliária --</option>
                    <?php foreach ($listaImobiliarias as $imob): ?>
                        <option value="<?= $imob['id_imobiliaria'] ?>" <?= ($id_imobiliaria_filtro == $imob['id_imobiliaria']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($imob['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        <?php endif; ?>

        <ul class="divide-y divide-gray-200">
            <?php foreach ($usuariosDisponiveis as $u): ?>
                <?php if ($u['id_usuario'] == $id_usuario_logado) continue; ?>
                <li>
                    <a href="chat.php?id_destino=<?= $u['id_usuario'] ?>&id_imobiliaria=<?= $id_imobiliaria_filtro ?>" class="block px-4 py-3 hover:bg-gray-100 rounded-lg">
                        <div class="font-semibold text-gray-800"><?= htmlspecialchars($u['nome']) ?></div>
                        <div class="text-sm text-gray-500">
                            <?= isset($ultimasMensagens[$u['id_usuario']]) ? htmlspecialchars(substr($ultimasMensagens[$u['id_usuario']]['mensagem'], 0, 30)) . '...' : 'Nenhuma conversa iniciada' ?>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Mensagens -->
    <div class="lg:col-span-3 bg-white rounded-xl shadow-md p-5">
        <h2 class="text-xl font-bold mb-4">Mensagens</h2>
        <div id="mensagens" class="h-[calc(100vh-300px)] overflow-y-auto bg-gray-50 rounded p-4 space-y-2 relative overflow-visible">

            <?php if (!$id_conversa_ativa): ?>
                <p class="text-center text-gray-400">Selecione um colaborador para iniciar a conversa.</p>
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

                <input type="text" maxlength="1000" name="mensagem" id="mensagem-input" class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Digite sua mensagem..." required autocomplete="off">
                <button type="button" id="emoji-btn" class="text-xl">😊</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">Enviar</button>
                <emoji-picker class="light absolute hidden z-50" style="bottom: 100%; right: 0;"></emoji-picker>
            </form>
        <?php endif; ?>
    </div>
</div>
<!-- Seletor de reação -->
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
    let pollingInterval;
    const seletorReacao = document.getElementById('seletor-reacao-global');

    function carregarMensagens() {
        const idConversa = <?= json_encode($id_conversa_ativa ?? null) ?>;
        if (!idConversa) return;
        fetch(`get_mensagens.php?id_conversa=${idConversa}&t=${Date.now()}`)
            .then(res => res.text())
            .then(html => {
                const mensagensBox = document.getElementById('mensagens');
                const isScrolledToBottom = mensagensBox.scrollTop + mensagensBox.clientHeight >= mensagensBox.scrollHeight - 30;
                mensagensBox.innerHTML = html;
                if (isScrolledToBottom) mensagensBox.scrollTop = mensagensBox.scrollHeight;
            });
    }

    function mostrarSeletorReacao(btn) {
        const mensagemDiv = btn.closest('.mensagem');
        const idMensagem = mensagemDiv.dataset.idMensagem;
        seletorReacao.dataset.idMensagem = idMensagem;

        const rect = btn.getBoundingClientRect();
        let top = rect.top - seletorReacao.offsetHeight - 5;
        let left;

        const isMinha = mensagemDiv.classList.contains('mensagem-direita');

        if (isMinha) {
            left = rect.left - seletorReacao.offsetWidth - 5; // à esquerda
        } else {
            left = rect.right + 5; // à direita
        }

        if (left < 5) left = 5;
        if (left + seletorReacao.offsetWidth > window.innerWidth) {
            left = window.innerWidth - seletorReacao.offsetWidth - 5;
        }

        seletorReacao.style.top = `${top}px`;
        seletorReacao.style.left = `${left}px`;
        seletorReacao.classList.remove('hidden');
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

    document.addEventListener('DOMContentLoaded', () => {
        const idConversa = <?= json_encode($id_conversa_ativa ?? null) ?>;
        if (idConversa) {
            carregarMensagens();
            pollingInterval = setInterval(carregarMensagens, 3000);
        }

        document.body.addEventListener('click', e => {
            // Abrir dropdown (3 pontinhos)
            const btnOpcoes = e.target.closest('.btn-opcoes');
            if (btnOpcoes) {
                e.stopPropagation();
                document.querySelectorAll('.dropdown-opcoes').forEach(d => d.classList.add('hidden'));
                const dropdown = btnOpcoes.nextElementSibling;
                if (dropdown) dropdown.classList.toggle('hidden');
                return;
            }

            // Botão de reação
            const btnReagir = e.target.closest('.btn-reagir');
            if (btnReagir) {
                e.stopPropagation();
                const idMensagem = btnReagir.dataset.idMensagem;
                seletorReacao.dataset.idMensagem = idMensagem;

                const rect = btnReagir.getBoundingClientRect();
                let top = rect.bottom + 5;
                let left = rect.left + rect.width / 2 - seletorReacao.offsetWidth / 2;

                if (left < 5) left = 5;
                if (left + seletorReacao.offsetWidth > window.innerWidth)
                    left = window.innerWidth - seletorReacao.offsetWidth - 5;

                seletorReacao.style.top = `${top}px`;
                seletorReacao.style.left = `${left}px`;
                seletorReacao.classList.remove('hidden');
                return;
            }

            // Clicou em um emoji
            const emoji = e.target.closest('.emoji-reacao');
            if (emoji) {
                const id = seletorReacao.dataset.idMensagem;
                enviarReacao(id, emoji.dataset.reacao);
                return;
            }

            // Clicou fora de tudo
            esconderSeletorReacao();
            document.querySelectorAll('.dropdown-opcoes').forEach(d => d.classList.add('hidden'));

            // Fechar emoji picker se necessário
            const emojiBtn = document.getElementById('emoji-btn');
            const emojiPicker = document.querySelector('emoji-picker');
            if (emojiBtn && emojiPicker) {
                if (!emojiBtn.contains(e.target) && !emojiPicker.contains(e.target)) {
                    emojiPicker.classList.add('hidden');
                }
            }
        });

        // Emoji picker
        const emojiBtn = document.getElementById('emoji-btn');
        const emojiPicker = document.querySelector('emoji-picker');
        const input = document.getElementById('mensagem-input');

        if (emojiBtn && emojiPicker) {
            emojiBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                emojiPicker.classList.toggle('hidden');
            });

            document.body.addEventListener('click', (e) => {
                if (!emojiBtn.contains(e.target) && !emojiPicker.contains(e.target)) {
                    emojiPicker.classList.add('hidden');
                }
            });

            emojiPicker.addEventListener('emoji-click', (e) => {
                input.value += e.detail.unicode;
                input.focus();
            });
        }
    });
</script>