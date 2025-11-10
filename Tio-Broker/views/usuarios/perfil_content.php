<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil</title>
    <!-- Tailwind CSS --><script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome --><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Estilos para o menu da foto */
        #foto-menu {
            transition: opacity 0.2s ease-out, transform 0.2s ease-out;
        }
        .modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex; align-items: center; justify-content: center;
            z-index: 60; opacity: 0; visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .modal-overlay.visible { opacity: 1; visibility: visible; }
        .modal-content {
            background: white; padding: 2rem; border-radius: 0.75rem;
            width: 90%; max-width: 450px;
            transform: scale(0.95); transition: transform 0.3s ease;
        }
        .modal-overlay.visible .modal-content { transform: scale(1); }
    </style>
</head>
<body class="bg-gray-50">

<!-- Contêiner principal com padding e alinhamento. --><div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">


    <!-- Card do Formulário --><div class="bg-white p-8 rounded-xl shadow-lg">
        
        <!-- NOVO: Cabeçalho com Título e Foto (Layout ajustado) --><div class="flex flex-col sm:flex-row items-center justify-between mb-10 gap-6">
            <h2 class="text-3xl font-bold text-gray-900">Meu Perfil</h2>
            <div class="relative group" id="foto-container">
                <img id="foto-preview" src="<?= !empty($usuario['foto']) ? '../../' . htmlspecialchars($usuario['foto']) : 'https://placehold.co/128x128/EFEFEF/A9A9A9?text=Perfil' ?>" alt="Foto de Perfil" class="w-48 h-48 rounded-full object-cover ring-2 ring-offset-2 ring-blue-200 cursor-pointer">
                <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 cursor-pointer" id="foto-overlay-trigger">
                     <i class="fas fa-camera text-white text-2xl"></i>
                </div>
                <!-- Menu Dropdown da Foto --><div id="foto-menu" class="absolute top-full right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 py-1 z-10 hidden origin-top-right">
                     <a href="#" id="upload-foto-btn" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-upload w-4"></i> Enviar nova foto</a>
                     <a href="#" id="remover-foto-menu-btn" class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50"><i class="fas fa-trash-alt w-4"></i> Remover foto</a>
                </div>
            </div>
        </div>

        <form action="../../controllers/UsuarioController.php" method="POST" enctype="multipart/form-data" class="space-y-10" id="perfil-form">
            <input id="foto" name="foto" type="file" class="sr-only" accept="image/png, image/jpeg, image/gif">
            <input type="hidden" name="action" value="atualizarPerfil">
            <input type="hidden" name="remover_foto" id="remover_foto_input" value="0">

            <!-- Seção de Informações Pessoais --><fieldset class="space-y-6">
                <legend class="text-xl font-semibold text-gray-800 mb-4">Informações Pessoais</legend>
                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                    <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($usuario['nome'] ?? 'Nome do Usuário') ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <!-- ✅✅✅ CAMPO DE E-MAIL CORRIGIDO ✅✅✅ --><label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($usuario['email'] ?? 'email@exemplo.com') ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                    <input type="text" name="telefone" id="telefone" value="<?= htmlspecialchars($usuario['telefone'] ?? '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="(99) 99999-9999">
                </div>
            </fieldset>

            <!-- Seção de Alteração de Senha --><fieldset class="border-t pt-8 space-y-6">
                <legend class="text-xl font-semibold text-gray-800">Segurança</legend>
                <p class="text-sm text-gray-500 -mt-4">Deixe os campos de senha em branco para não alterá-la.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <!-- Coluna da Nova Senha --><div>
                        <label for="senha" class="block text-sm font-medium text-gray-700">Nova Senha</label>
                        <div class="relative mt-1">
                            <input type="password" name="senha" id="senha" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-10" autocomplete="new-password">
                            <button type="button" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-blue-600" id="toggle-senha">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Coluna da Confirmação de Senha --><div>
                        <label for="confirmar_senha" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                        <div class="relative mt-1">
                            <input type="password" name="confirmar_senha" id="confirmar_senha" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-10" autocomplete="new-password">
                            <button type="button" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-blue-600" id="toggle-confirmar-senha">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Requisitos da Senha (inicialmente ocultos) --><div id="password-requirements-container" class="md:col-span-2 mt-2 hidden">
                        <p class="text-sm font-medium text-gray-600">A senha deve ter:</p>
                        <ul class="text-sm space-y-1 mt-2">
                            <li id="req-length" class="text-gray-500 flex items-center"><i class="fas fa-times-circle text-red-400 mr-2"></i> Pelo menos 8 caracteres</li>
                            <li id="req-case" class="text-gray-500 flex items-center"><i class="fas fa-times-circle text-red-400 mr-2"></i> Letras maiúsculas e minúsculas</li>
                            <li id="req-number" class="text-gray-500 flex items-center"><i class="fas fa-times-circle text-red-400 mr-2"></i> Pelo menos um número</li>
                            <li id="req-symbol" class="text-gray-500 flex items-center"><i class="fas fa-times-circle text-red-400 mr-2"></i> Pelo menos um símbolo (!, @, #...)</li>
                            <li id="req-match" class="text-gray-500 flex items-center"><i class="fas fa-times-circle text-red-400 mr-2"></i> As senhas devem coincidir</li>
                        </ul>
                    </div>
                </div>
            </fieldset>

            <!-- Botão de Submissão --><div class="flex justify-end pt-8 border-t">
                <button type="submit" id="submit-btn" class="bg-blue-600 text-white px-8 py-2.5 rounded-lg font-semibold text-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center gap-2 w-full sm:w-auto">
                    <span id="submit-text">Salvar Alterações</span>
                    <i id="submit-spinner" class="fas fa-spinner fa-spin hidden"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Confirmação para Remover Foto --><div id="confirmation-modal" class="modal-overlay">
    <div class="modal-content text-center">
        <i class="fas fa-exclamation-triangle text-4xl text-yellow-400 mb-4"></i>
        <h3 class="text-lg font-bold text-gray-800 mb-2">Remover Foto de Perfil</h3>
        <p class="text-gray-600 mb-6">Você tem certeza que deseja remover sua foto de perfil?</p>
        <div class="flex justify-center gap-4">
            <button id="modal-cancel-btn" class="py-2 px-6 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300">Cancelar</button>
            <button id="modal-confirm-btn" class="py-2 px-6 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700">Sim, Remover</button>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Seleção de Elementos ---
        const form = document.getElementById('perfil-form');
        const telefoneInput = document.getElementById('telefone');
        const senhaInput = document.getElementById('senha');
        const confirmarSenhaInput = document.getElementById('confirmar_senha');
        const fotoInput = document.getElementById('foto');
        const fotoPreview = document.getElementById('foto-preview');
        const removerFotoInput = document.getElementById('remover_foto_input');
        const submitBtn = document.getElementById('submit-btn');
        const submitText = document.getElementById('submit-text');
        const submitSpinner = document.getElementById('submit-spinner');
        const placeholderSrc = 'https://placehold.co/128x128/EFEFEF/A9A9A9?text=Perfil'; /* Atualizado */
        const passwordRequirementsContainer = document.getElementById('password-requirements-container');
        
        // Elementos do menu da foto e modal
        const fotoContainer = document.getElementById('foto-container');
        const fotoMenu = document.getElementById('foto-menu');
        const uploadFotoBtn = document.getElementById('upload-foto-btn');
        const removerFotoMenuBtn = document.getElementById('remover-foto-menu-btn');
        const modal = document.getElementById('confirmation-modal');
        const cancelBtn = document.getElementById('modal-cancel-btn');
        const confirmBtn = document.getElementById('modal-confirm-btn');


        // --- Máscara de Telefone ---
        const initPhoneMask = () => {
            const mascarar = (input) => {
                if (!input) return;
                let value = input.value.replace(/\D/g, '').slice(0, 11);
                value = value.replace(/^(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
                input.value = value;
            };
            if (telefoneInput) {
                telefoneInput.addEventListener('input', () => mascarar(telefoneInput));
                mascarar(telefoneInput);
            }
        };

        // --- Validação de Senha ---
        const initPasswordValidation = () => {
            const requirements = { length: document.getElementById('req-length'), case: document.getElementById('req-case'), number: document.getElementById('req-number'), symbol: document.getElementById('req-symbol'), match: document.getElementById('req-match'), };
            const validateRequirement = (element, isValid) => { if (!element) return; const icon = element.querySelector('i'); if (!icon) return; const validClass = 'text-green-600'; const invalidClass = 'text-red-500'; const validIcon = 'fas fa-check-circle text-green-500 mr-2'; const invalidIcon = 'fas fa-times-circle text-red-400 mr-2'; element.classList.remove(validClass, invalidClass, 'text-gray-500'); if (isValid) { element.classList.add(validClass); icon.className = validIcon; } else { element.classList.add(invalidClass); icon.className = invalidIcon; } };
            const resetValidationUI = () => { for (const key in requirements) { const li = requirements[key]; if (li) { li.classList.remove('text-green-600', 'text-red-500'); li.classList.add('text-gray-500'); const icon = li.querySelector('i'); if (icon) icon.className = 'fas fa-times-circle text-red-400 mr-2'; } } };
            const validatePassword = () => {
                const senha = senhaInput.value; const confirmar = confirmarSenhaInput.value;
                if (senha === '' && confirmar === '') { passwordRequirementsContainer.classList.add('hidden'); resetValidationUI(); return false; } else { passwordRequirementsContainer.classList.remove('hidden'); }
                const criteria = { length: senha.length >= 8, case: /[a-z]/.test(senha) && /[A-Z]/.test(senha), number: /\d/.test(senha), symbol: /[^A-Za-z0-9]/.test(senha) };
                const allPrimaryReqsMet = criteria.length && criteria.case && criteria.number && criteria.symbol;
                criteria.match = allPrimaryReqsMet && senha === confirmar && senha !== '';
                for (const key in criteria) { if (requirements.hasOwnProperty(key) && requirements[key]) { validateRequirement(requirements[key], criteria[key]); } }
                return allPrimaryReqsMet && criteria.match;
            };
            senhaInput.addEventListener('input', validatePassword); confirmarSenhaInput.addEventListener('input', validatePassword);
            document.getElementById('toggle-senha').addEventListener('click', () => { const icon = document.querySelector('#toggle-senha i'); senhaInput.type = senhaInput.type === 'password' ? 'text' : 'password'; icon.className = senhaInput.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash'; });
            document.getElementById('toggle-confirmar-senha').addEventListener('click', () => { const icon = document.querySelector('#toggle-confirmar-senha i'); confirmarSenhaInput.type = confirmarSenhaInput.type === 'password' ? 'text' : 'password'; icon.className = confirmarSenhaInput.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash'; });
            return validatePassword;
        };

        // --- Gerenciamento da Foto, Menu e Modal ---
        const initPhotoUpload = () => {
            const openModal = () => modal.classList.add('visible');
            const closeModal = () => modal.classList.remove('visible');
            const toggleFotoMenu = () => fotoMenu.classList.toggle('hidden');

            fotoContainer.addEventListener('click', (e) => { e.stopPropagation(); toggleFotoMenu(); });
            uploadFotoBtn.addEventListener('click', (e) => { e.preventDefault(); fotoInput.click(); toggleFotoMenu(); });
            removerFotoMenuBtn.addEventListener('click', (e) => { e.preventDefault(); openModal(); toggleFotoMenu(); });
            document.addEventListener('click', (e) => { if (!fotoContainer.contains(e.target)) { fotoMenu.classList.add('hidden'); } });

            fotoInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => fotoPreview.src = event.target.result;
                    reader.readAsDataURL(file);
                    removerFotoInput.value = '0';
                }
            });
            
            cancelBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
            confirmBtn.addEventListener('click', () => {
                fotoPreview.src = placeholderSrc;
                fotoInput.value = '';
                removerFotoInput.value = '1';
                closeModal();
            });
        };

        // --- Submissão do Formulário ---
        const initFormSubmission = (passwordValidator) => {
            if (form && submitBtn) {
                form.addEventListener('submit', (e) => {
                    const pass = senhaInput.value;
                    if (pass !== '' && !passwordValidator()) {
                        e.preventDefault();
                        alert('Por favor, corrija os erros na senha antes de salvar.');
                        senhaInput.focus();
                        return;
                    }
                    submitBtn.disabled = true;
                    submitText.textContent = 'Salvando...';
                    submitSpinner.classList.remove('hidden');
                });
            }
        };

        // --- Inicializando todos os módulos ---
        initPhoneMask();
        const passwordValidator = initPasswordValidation();
        initPhotoUpload();
        initFormSubmission(passwordValidator);
    });
</script>

</body>
</html>

