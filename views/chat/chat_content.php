<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: views/chat/chat_content.php (VERSÃO COM MELHORIAS DE UX)
|--------------------------------------------------------------------------
| - Adicionado scroll suave para novas mensagens.
| - O balão de notificação agora desaparece instantaneamente ao clicar numa conversa.
*/
?>
<!-- Inclusão do Tailwind CSS e Fontes -->
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style> 
    body { font-family: 'Inter', sans-serif; } 
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
</style>

<div class="flex h-full w-full rounded-xl shadow-md bg-white overflow-hidden border border-gray-200/80">
    
    <!-- Coluna de Usuários (ASIDE) -->
    <aside class="w-full md:w-1/3 lg:w-1/4 flex flex-col border-r border-gray-200/80">
        <div class="p-4 border-b border-gray-200/80 flex-shrink-0">
            <h1 class="text-xl font-semibold text-gray-800">Conversas</h1>
        </div>
        
        <div class="p-4 flex-shrink-0 space-y-4">
            <?php if ($permissao === 'SuperAdmin'): ?>
                <form method="GET">
                    <select name="id_imobiliaria" onchange="this.form.submit()" class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-1 focus:ring-violet-400 focus:border-violet-400 transition">
                        <option value="">-- Todas as Imobiliárias --</option>
                        <?php foreach ($listaImobiliarias as $imob): ?>
                            <option value="<?= $imob['id_imobiliaria'] ?>" <?= ($id_imobiliaria_filtro == $imob['id_imobiliaria']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($imob['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php endif; ?>
            <input type="text" id="busca-usuario" placeholder="Buscar colaborador..." class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-1 focus:ring-violet-400 focus:border-violet-400 transition">
        </div>

        <ul id="lista-usuarios" class="flex-grow overflow-y-auto custom-scrollbar">
            <?php foreach ($usuariosDisponiveis as $u): ?>
                <?php if ($u['id_usuario'] == $id_usuario_logado) continue; ?>
                <?php $is_active = (isset($id_destino) && $id_destino == $u['id_usuario']); ?>
                <li class="user-item" data-user-id="<?= $u['id_usuario'] ?>" data-user-name="<?= htmlspecialchars(mb_strtolower($u['nome'])) ?>" data-timestamp="<?= isset($ultimasMensagens[$u['id_usuario']]) ? strtotime($ultimasMensagens[$u['id_usuario']]['data_envio']) : 0 ?>">
                    <a href="chat.php?id_destino=<?= $u['id_usuario'] ?>&id_imobiliaria=<?= $id_imobiliaria_filtro ?>" class="flex items-center gap-3 px-4 py-3 transition-colors duration-200 border-r-4 <?= $is_active ? 'bg-violet-50 border-violet-500' : 'border-transparent hover:bg-gray-100' ?>">
                        <div class="relative w-11 h-11 flex-shrink-0">
                            <img src="<?= !empty($u['foto']) ? '../../uploads/' . htmlspecialchars($u['foto']) : 'https://placehold.co/100x100/c4b5fd/4c1d95?text=' . mb_strtoupper(mb_substr($u['nome'], 0, 1)) ?>" class="w-full h-full rounded-full object-cover">
                            <span id="status-dot-<?= $u['id_usuario'] ?>" class="absolute bottom-0 right-0 block h-3 w-3 bg-gray-400 rounded-full border-2 border-white"></span>

                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-center">
                                <p class="font-semibold text-gray-800 truncate text-sm"><?= htmlspecialchars($u['nome']) ?></p>
                                <span class="text-xs text-gray-400" data-last-message-time-for="<?= $u['id_usuario'] ?>"><?= isset($ultimasMensagens[$u['id_usuario']]) ? date('H:i', strtotime($ultimasMensagens[$u['id_usuario']]['data_envio'])) : '' ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <p class="text-sm text-gray-500 truncate" data-last-message-text-for="<?= $u['id_usuario'] ?>"><?= isset($ultimasMensagens[$u['id_usuario']]) ? htmlspecialchars($ultimasMensagens[$u['id_usuario']]['mensagem']) : 'Nenhuma conversa.' ?></p>
                                <span class="bg-violet-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center <?= (!isset($notificacoes[$u['id_usuario']]) || $notificacoes[$u['id_usuario']] == 0) ? 'hidden' : '' ?>" data-unread-count-for="<?= $u['id_usuario'] ?>"><?= $notificacoes[$u['id_usuario']] ?? '' ?></span>
                            </div>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

     <!-- Coluna da Conversa (MAIN) -->
    <main class="w-full flex flex-col bg-gray-50">
        <?php if ($id_conversa_ativa && $usuarioDestino): ?>
            <header class="flex items-center gap-4 p-4 border-b border-gray-200/80 flex-shrink-0 bg-white">
                <img src="<?= !empty($usuarioDestino['foto']) ? '../../uploads/' . htmlspecialchars($usuarioDestino['foto']) : 'https://placehold.co/100x100/c4b5fd/4c1d95?text=' . mb_strtoupper(mb_substr($usuarioDestino['nome'], 0, 1)) ?>" class="w-11 h-11 rounded-full object-cover">
                <div>
                    <h2 class="text-base font-semibold text-gray-900"><?= htmlspecialchars($usuarioDestino['nome']) ?></h2>
                    <!-- **NOVO**: Indicador de Status no Cabeçalho -->
                    <p id="header-status-text" class="text-sm text-gray-500">Offline</p>
                </div>
            </header>
            <!-- ... (resto do main continua igual) ... -->
            <div id="mensagens" class="flex-grow p-6 overflow-y-auto custom-scrollbar bg-slate-50"></div>
            <div id="typing-indicator" class="h-6 px-6 pb-2 text-sm text-gray-500 italic hidden">
                <span id="typing-user-name"></span> está a digitar...
            </div>
            <footer class="p-4 bg-white/80 backdrop-blur-sm border-t border-gray-200/80 flex-shrink-0">
                <form id="form-mensagem" class="flex items-center gap-3">
                    <input type="text" id="mensagem-input" class="flex-1 bg-gray-100 border-gray-300 rounded-full px-5 py-3 text-sm focus:ring-1 focus:ring-violet-400 focus:border-violet-400 transition" placeholder="Digite sua mensagem..." autocomplete="off">
                    <button type="submit" class="p-3 bg-violet-500 text-white rounded-full hover:bg-violet-600 transition-colors">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.428A1 1 0 009.172 15V4.828a1 1 0 00-1.828-.553L4.22 7.586l-1.414-1.414 3.536-3.536a1 1 0 011.414 0l3.536 3.536-1.414 1.414-2.121-2.121z"/></svg>
                    </button>
                </form>
            </footer>
        <?php else: ?>
            <div class="flex items-center justify-center h-full text-center text-gray-500">
                <p>Selecione uma conversa para começar.</p>
            </div>
        <?php endif; ?>
    </main>
</div>
<!-- **NOVO**: HTML para o seletor de emojis -->
<div id="seletor-reacao" class="fixed bg-white rounded-lg shadow-lg p-2 flex gap-2 hidden z-50">
    <span class="emoji-reacao text-xl cursor-pointer" data-reacao="👍">👍</span>
    <span class="emoji-reacao text-xl cursor-pointer" data-reacao="❤️">❤️</span>
    <span class="emoji-reacao text-xl cursor-pointer" data-reacao="😂">😂</span>
    <span class="emoji-reacao text-xl cursor-pointer" data-reacao="😮">😮</span>
    <span class="emoji-reacao text-xl cursor-pointer" data-reacao="😢">😢</span>
    <span class="emoji-reacao text-xl cursor-pointer" data-reacao="🙏">🙏</span>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- VARIÁVEIS DO PHP PARA O JS ---
    const idConversaAtiva = <?= json_encode($id_conversa_ativa ?? null) ?>;
    const idUsuarioLogado = <?= json_encode($id_usuario_logado ?? null) ?>;
    const idDestino = <?= json_encode($id_destino ?? null) ?>;
    const infoUsuarioLogado = {
        nome: '<?= htmlspecialchars($_SESSION['usuario']['nome'] ?? 'Eu', ENT_QUOTES) ?>',
        foto: '<?= !empty($_SESSION['usuario']['foto']) ? "../../uploads/" . htmlspecialchars($_SESSION['usuario']['foto'], ENT_QUOTES) : "https://placehold.co/100x100/c4b5fd/4c1d95?text=E" ?>'
    };

    // --- ELEMENTOS DO DOM ---
    const mensagensContainer = document.getElementById('mensagens');
    const mensagemForm = document.getElementById('form-mensagem');
    const mensagemInput = document.getElementById('mensagem-input');
    const listaUsuariosEl = document.getElementById('lista-usuarios');
    const typingIndicator = document.getElementById('typing-indicator');
    const typingUserName = document.getElementById('typing-user-name');
    
    let typingTimer;

    // --- LÓGICA DO WEBSOCKET ---
    const conn = new WebSocket('ws://localhost:8080');

    conn.onopen = () => {
        console.log("Conexão WebSocket estabelecida!");
        if (idUsuarioLogado) {
            conn.send(JSON.stringify({ action: 'register', id_usuario: idUsuarioLogado }));
        }
        if (idConversaAtiva) {
            conn.send(JSON.stringify({ action: 'subscribe', id_conversa: idConversaAtiva }));
            carregarHistorico();
        }
    };

    conn.onmessage = (e) => {
        const data = JSON.parse(e.data);
        
        switch(data.action) {
            case 'message':
                if (data.id_conversa === idConversaAtiva) {
                    typingIndicator.classList.add('hidden');
                    renderizarMensagem(data, false);
                    atualizarItemDaLista(data.id_usuario, data.mensagem);
                }
                break;
            case 'notification':
                atualizarListaComNotificacao(data.from_user_id, data.message_text);
                break;
            case 'typing_start':
                if (data.id_conversa === idConversaAtiva) {
                    typingUserName.textContent = data.nome_usuario;
                    typingIndicator.classList.remove('hidden');
                }
                break;
            case 'typing_stop':
                 if (data.id_conversa === idConversaAtiva) {
                    typingIndicator.classList.add('hidden');
                }
                break;
        }
    };

    if (mensagemForm) {
        mensagemForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const textoMensagem = mensagemInput.value.trim();
            if (textoMensagem && conn.readyState === WebSocket.OPEN) {
                conn.send(JSON.stringify({ action: 'typing_stop', id_conversa: idConversaAtiva }));
                const data = {
                    action: 'message',
                    id_conversa: idConversaAtiva,
                    id_usuario: idUsuarioLogado,
                    id_destino: idDestino,
                    mensagem: textoMensagem,
                    nome_usuario: infoUsuarioLogado.nome,
                    foto: infoUsuarioLogado.foto
                };
                conn.send(JSON.stringify(data));
                renderizarMensagem(data, true);
                
                atualizarItemDaLista(idDestino, textoMensagem);
                
                mensagemInput.value = '';
            }
        });
    }
    
    if (mensagemInput) {
        mensagemInput.addEventListener('input', () => {
            clearTimeout(typingTimer);
            conn.send(JSON.stringify({ action: 'typing_start', id_conversa: idConversaAtiva, nome_usuario: infoUsuarioLogado.nome }));
            typingTimer = setTimeout(() => {
                conn.send(JSON.stringify({ action: 'typing_stop', id_conversa: idConversaAtiva }));
            }, 2000);
        });
    }
    
    // **NOVO**: Event listener para limpar a notificação ao clicar na conversa
    if (listaUsuariosEl) {
        listaUsuariosEl.addEventListener('click', (e) => {
            const userItemLink = e.target.closest('.user-item a');
            if (!userItemLink) return;

            const userItem = userItemLink.parentElement;
            const userId = userItem.dataset.userId;
            const contadorEl = userItem.querySelector(`[data-unread-count-for="${userId}"]`);
            
            // Esconde a notificação imediatamente para uma melhor UX
            if (contadorEl && !contadorEl.classList.contains('hidden')) {
                contadorEl.classList.add('hidden');
                contadorEl.textContent = '0';
            }
        });
    }
    
    // --- FUNÇÕES AUXILIARES ---
     // **NOVO**: Funções para atualizar o status na UI
    function updateUserStatus(userId, isOnline) {
        const statusDot = document.getElementById(`status-dot-${userId}`);
        if (statusDot) {
            statusDot.classList.toggle('bg-green-500', isOnline);
            statusDot.classList.toggle('bg-gray-400', !isOnline);
        }
        // Atualiza o cabeçalho se for o utilizador da conversa ativa
        if (userId == idDestino && headerStatusText) {
            headerStatusText.textContent = isOnline ? 'Online' : 'Offline';
            headerStatusText.classList.toggle('text-green-600', isOnline);
            headerStatusText.classList.toggle('text-gray-500', !isOnline);
        }
    }

    function updateAllUserStatus(onlineUserIds) {
        document.querySelectorAll('.user-item').forEach(item => {
            const userId = item.dataset.userId;
            const isOnline = onlineUserIds.includes(parseInt(userId, 10));
            updateUserStatus(userId, isOnline);
        });
    }
    
    function atualizarItemDaLista(userId, messageText) {
        const userItem = document.querySelector(`.user-item[data-user-id="${userId}"]`);
        if (userItem) {
            userItem.querySelector(`[data-last-message-text-for="${userId}"]`).textContent = messageText.substring(0, 25) + '...';
            userItem.querySelector(`[data-last-message-time-for="${userId}"]`).textContent = new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
            userItem.dataset.timestamp = Math.floor(Date.now() / 1000);
            const sortedItems = Array.from(document.querySelectorAll('.user-item')).sort((a, b) => b.dataset.timestamp - a.dataset.timestamp);
            sortedItems.forEach(item => listaUsuariosEl.appendChild(item));
        }
    }

    function atualizarListaComNotificacao(fromUserId, messageText) {
        const userItem = document.querySelector(`.user-item[data-user-id="${fromUserId}"]`);
        if (userItem) {
            const contadorEl = userItem.querySelector(`[data-unread-count-for="${fromUserId}"]`);
            let currentCount = parseInt(contadorEl.textContent) || 0;
            contadorEl.textContent = currentCount + 1;
            contadorEl.classList.remove('hidden');
            atualizarItemDaLista(fromUserId, messageText);
        }
    }

    function carregarHistorico() {
        if (!idConversaAtiva) return;
        fetch(`get_mensagens.php?id_conversa=${idConversaAtiva}&t=${Date.now()}`)
            .then(res => res.text())
            .then(html => {
                if (mensagensContainer) {
                    mensagensContainer.innerHTML = html;
                    mensagensContainer.scrollTop = mensagensContainer.scrollHeight;
                }
            });
    }

    function renderizarMensagem(data, isMinha) {
        if (!mensagensContainer) return;
        const alinhamento = isMinha ? 'justify-end' : 'justify-start';
        const bolhaClasses = isMinha ? 'bg-violet-600 text-white rounded-br-lg' : 'bg-white text-gray-800 border border-gray-200 rounded-bl-lg';
        const mensagemHtml = `
            <div class="w-full flex ${alinhamento} mt-1">
                <div class="flex ${isMinha ? 'flex-row-reverse' : 'flex-row'} items-start gap-3 max-w-[80%]">
                    <div class="w-10 h-10 flex-shrink-0">
                        <img src="${data.foto}" class="w-full h-full rounded-full object-cover">
                    </div>
                    <div class="flex flex-col gap-1">
                        <div class="p-3 rounded-2xl shadow-sm ${bolhaClasses}">
                            <p class="text-sm">${data.mensagem.replace(/\n/g, '<br>')}</p>
                        </div>
                        <span class="text-xs text-gray-500 px-2 ${isMinha ? 'self-end' : 'self-start'}">${new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}</span>
                    </div>
                </div>
            </div>
        `;
        mensagensContainer.insertAdjacentHTML('beforeend', mensagemHtml);
        // **NOVO**: Scroll suave para a nova mensagem
        mensagensContainer.scrollTo({
            top: mensagensContainer.scrollHeight,
            behavior: 'smooth'
        });
    }

    const buscaInput = document.getElementById('busca-usuario');
    if (buscaInput) {
        buscaInput.addEventListener('keyup', () => {
            const termo = buscaInput.value.toLowerCase();
            document.querySelectorAll('.user-item').forEach(item => {
                const nome = item.dataset.userName || '';
                item.style.display = nome.includes(termo) ? 'block' : 'none';
            });
        });
    }
    // **NOVO**: Event listener para o corpo do documento para lidar com reações
    document.body.addEventListener('click', function(e) {
        const seletorReacao = document.getElementById('seletor-reacao');

        // Mostra o seletor de reações
        if (e.target.classList.contains('btn-reagir')) {
            e.stopPropagation(); // Impede que o clique feche o seletor imediatamente
            const idMensagem = e.target.dataset.idMensagem;
            seletorReacao.dataset.idMensagem = idMensagem;
            
            const rect = e.target.getBoundingClientRect();
            seletorReacao.style.top = `${window.scrollY + rect.top - seletorReacao.offsetHeight - 5}px`;
            seletorReacao.style.left = `${window.scrollX + rect.left}px`;
            seletorReacao.classList.remove('hidden');
            return;
        }
        
        // Envia a reação selecionada
        if (e.target.classList.contains('emoji-reacao')) {
            const idMensagem = seletorReacao.dataset.idMensagem;
            const reacao = e.target.dataset.reacao;

            conn.send(JSON.stringify({
                action: 'reaction',
                id_conversa: idConversaAtiva,
                id_mensagem: idMensagem,
                reacao: reacao
            }));
            seletorReacao.classList.add('hidden');
            return;
        }

        // Esconde o seletor se clicar fora
        if (seletorReacao && !seletorReacao.contains(e.target)) {
            seletorReacao.classList.add('hidden');
        }
    });
});
</script>
