<?php
// Garante que a sessão seja iniciada para podermos acessar os dados do usuário.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-i18n="title">Configurações da Conta</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome para Ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilo customizado para o toggle switch */
        .toggle-checkbox:checked {
            right: 0;
            border-color: #3b82f6;
        }

        .toggle-checkbox:checked+.toggle-label {
            background-color: #3b82f6;
        }

        /* **NOVO**: Esconde o texto que está aguardando tradução para evitar o "pisca-pisca" */
        .translating {
            visibility: hidden;
        }
    </style>
</head>

<body class="bg-gray-50">

    <main class="max-w-4xl mx-auto p-4 md:p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 translating" data-i18n="title">Configurações da Conta</h1>

        <!-- Container do formulário -->
        <div class="bg-white p-8 rounded-lg shadow-md space-y-8">

            <!-- Seção de Acessibilidade -->
            <div>
                <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4 translating" data-i18n="accessibility.title">Acessibilidade</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <label for="narrador" class="text-gray-600 translating" data-i18n="accessibility.screenReader">Leitor de Tela (Narrador)</label>
                        <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                            <input type="checkbox" name="narrador" id="narrador" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" />
                            <label for="narrador" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="text-gray-600 translating" data-i18n="accessibility.fontSize">Tamanho da Fonte</label>
                        <div class="flex space-x-2">
                            <button data-font-size="text-sm" class="font-btn px-3 py-1 border rounded-md hover:bg-gray-100 translating" data-i18n="accessibility.fontSmall">Pequeno</button>
                            <button data-font-size="text-base" class="font-btn px-3 py-1 border rounded-md hover:bg-gray-100 translating" data-i18n="accessibility.fontMedium">Médio</button>
                            <button data-font-size="text-lg" class="font-btn px-3 py-1 border rounded-md hover:bg-gray-100 translating" data-i18n="accessibility.fontLarge">Grande</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção de Aparência -->
            <div>
                <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4 translating" data-i18n="appearance.title">Aparência</h2>
                <div class="flex items-center justify-between">
                    <label class="text-gray-600 translating" data-i18n="appearance.theme">Tema</label>
                    <div class="flex items-center space-x-4">
                        <label><input type="radio" name="theme" value="light"> <span class="translating" data-i18n="appearance.light">Claro</span></label>
                        <label><input type="radio" name="theme" value="dark"> <span class="translating" data-i18n="appearance.dark">Escuro</span></label>
                    </div>
                </div>
            </div>

            <!-- Seção de Idioma -->
            <div>
                <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4 translating" data-i18n="language.title">Idioma</h2>
                <div class="flex items-center justify-between">
                    <label for="language" class="text-gray-600 translating" data-i18n="language.select">Selecione o idioma</label>
                    <select id="language" name="language" class="border rounded-md px-3 py-2">
                        <option value="pt-br">Português (Brasil)</option>
                        <option value="en">English</option>
                        <option value="es">Español</option>
                    </select>
                </div>
            </div>

            <!-- Seção de Notificações -->
            <div>
                <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4 translating" data-i18n="notifications.title">Notificações</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <label for="sound-notifications" class="text-gray-600 translating" data-i18n="notifications.sound">Notificações Sonoras</label>
                        <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                            <input type="checkbox" name="sound-notifications" id="sound-notifications" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" />
                            <label for="sound-notifications" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <label for="visual-notifications" class="text-gray-600 translating" data-i18n="notifications.visual">Notificações Visuais (Pop-ups)</label>
                        <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                            <input type="checkbox" name="visual-notifications" id="visual-notifications" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" />
                            <label for="visual-notifications" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção Sobre -->
            <div>
                <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4 translating" data-i18n="about.title">Sobre</h2>
                <div class="flex flex-col space-y-2 text-blue-600">
                    <a href="#" class="hover:underline translating" data-i18n="about.privacy">Política de Privacidade</a>
                    <a href="#" class="hover:underline translating" data-i18n="about.terms">Termos de Serviço</a>
                    <a href="#" class="hover:underline translating" data-i18n="about.help">Ajuda e Suporte</a>
                    <a href="#" class="hover:underline translating" data-i18n="about.feedback">Enviar Feedback</a>
                </div>
            </div>

            <!-- Botão de Salvar -->
            <div class="flex justify-end pt-4">
                <button id="save-settings-btn" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 transition duration-300 translating" data-i18n="buttons.save">
                    Salvar Alterações
                </button>
            </div>

            <!-- Mensagem de feedback -->
            <div id="feedback-message" class="hidden text-center p-4 mt-4 rounded-md"></div>

        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userSettingsFromPHP = <?= json_encode($_SESSION['usuario']['configuracoes'] ?? null); ?>;

            let translations = {};

            async function loadTranslations(module, lang) {
                try {
                    const response = await fetch(`../../controllers/TraducaoController.php?modulo=${module}&lang=${lang}`);
                    const result = await response.json();
                    if (result.success) {
                        translations = result.data;
                    }
                } catch (error) {
                    console.error(`Could not load translation for module "${module}":`, error);
                }
            }

            function t(key) {
                return key.split('.').reduce((obj, i) => obj && obj[i], translations) || key;
            }

            function applyTranslationsToDOM() {
                document.querySelectorAll('[data-i18n]').forEach(el => {
                    const key = el.dataset.i18n;
                    const translation = t(key);
                    if (translation !== key) {
                        if (el.tagName === 'TITLE') {
                            el.textContent = translation;
                        } else {
                            el.innerText = translation;
                        }
                    }
                    // Remove a classe para mostrar o texto já traduzido
                    el.classList.remove('translating');
                });
            }

            const narratorCheckbox = document.getElementById('narrador');
            const fontBtns = document.querySelectorAll('.font-btn');
            const themeRadios = document.querySelectorAll('input[name="theme"]');
            const languageSelect = document.getElementById('language');
            const soundNotificationsCheckbox = document.getElementById('sound-notifications');
            const visualNotificationsCheckbox = document.getElementById('visual-notifications');
            const saveBtn = document.getElementById('save-settings-btn');
            const feedbackMessage = document.getElementById('feedback-message');

            let selectedFontSize = 'text-base';

            function loadSettings() {
                const settings = userSettingsFromPHP || JSON.parse(localStorage.getItem('userSettings'));
                if (!settings) return;

                narratorCheckbox.checked = settings.accessibility?.narrator || false;
                selectedFontSize = settings.accessibility?.fontSize || 'text-base';
                updateFontUI();

                const theme = settings.appearance?.theme || 'light';
                document.querySelector(`input[name="theme"][value="${theme}"]`).checked = true;
                if (theme === 'dark') document.documentElement.classList.add('dark');
                else document.documentElement.classList.remove('dark');

                languageSelect.value = settings.language || 'pt-br';

                soundNotificationsCheckbox.checked = settings.notifications?.sound ?? true;
                visualNotificationsCheckbox.checked = settings.notifications?.visual ?? true;
            }

            function saveSettings() {
                const oldLang = userSettingsFromPHP?.language || localStorage.getItem('calendarLang') || 'pt-br';

                const settings = {
                    accessibility: {
                        narrator: narratorCheckbox.checked,
                        fontSize: selectedFontSize
                    },
                    appearance: {
                        theme: document.querySelector('input[name="theme"]:checked').value
                    },
                    language: languageSelect.value,
                    notifications: {
                        sound: soundNotificationsCheckbox.checked,
                        visual: visualNotificationsCheckbox.checked
                    }
                };

                const newLang = settings.language;

                localStorage.setItem('userSettings', JSON.stringify(settings));
                localStorage.setItem('calendarLang', newLang);

                saveBtn.textContent = t('buttons.saving');
                saveBtn.disabled = true;

                fetch('../../controllers/ConfiguracaoController.php?action=salvar', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(settings)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showFeedback(t('feedback.success'), 'success');
                            if (oldLang !== newLang) {
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            }
                        } else {
                            throw new Error(data.message || t('feedback.error'));
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        showFeedback(error.message, 'error');
                    })
                    .finally(() => {
                        if (oldLang === newLang) {
                            setTimeout(() => {
                                saveBtn.textContent = t('buttons.save');
                                saveBtn.disabled = false;
                                feedbackMessage.classList.add('hidden');
                            }, 3000);
                        }
                    });
            }

            function showFeedback(message, type) {
                feedbackMessage.textContent = message;
                feedbackMessage.className = type === 'success' ?
                    'text-center p-4 mt-4 rounded-md bg-green-100 text-green-700' :
                    'text-center p-4 mt-4 rounded-md bg-red-100 text-red-700';
            }

            function updateFontUI() {
                fontBtns.forEach(b => b.classList.remove('bg-blue-100', 'text-blue-600'));
                const activeBtn = document.querySelector(`.font-btn[data-font-size="${selectedFontSize}"]`);
                if (activeBtn) activeBtn.classList.add('bg-blue-100', 'text-blue-600');
                document.body.className = `bg-gray-50 ${selectedFontSize}`;
            }

            fontBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    selectedFontSize = this.dataset.fontSize;
                    updateFontUI();
                });
            });

            themeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'dark') document.documentElement.classList.add('dark');
                    else document.documentElement.classList.remove('dark');
                });
            });

            saveBtn.addEventListener('click', saveSettings);

            async function initialize() {
                const initialLang = userSettingsFromPHP?.language || localStorage.getItem('calendarLang') || 'pt-br';

                await loadTranslations('configuracao', initialLang);
                applyTranslationsToDOM();
                loadSettings();
            }

            initialize();
        });
    </script>
</body>

</html>