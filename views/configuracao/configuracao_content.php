<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações da Conta</title>
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
    </style>
</head>

<body class="bg-gray-50">

    <main class="max-w-4xl mx-auto p-4 md:p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Configurações da Conta</h1>

        <!-- Container do formulário -->
        <div class="bg-white p-8 rounded-lg shadow-md space-y-8">

            <!-- Seção de Acessibilidade -->
            <div>
                <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4">Acessibilidade</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <label for="narrador" class="text-gray-600">Leitor de Tela (Narrador)</label>
                        <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                            <input type="checkbox" name="narrador" id="narrador" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" />
                            <label for="narrador" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="text-gray-600">Tamanho da Fonte</label>
                        <div class="flex space-x-2">
                            <button data-font-size="text-sm" class="font-btn px-3 py-1 border rounded-md hover:bg-gray-100">Pequeno</button>
                            <button data-font-size="text-base" class="font-btn px-3 py-1 border rounded-md hover:bg-gray-100 bg-blue-100 text-blue-600">Médio</button>
                            <button data-font-size="text-lg" class="font-btn px-3 py-1 border rounded-md hover:bg-gray-100">Grande</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção de Aparência -->
            <div>
                <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4">Aparência</h2>
                <div class="flex items-center justify-between">
                    <label class="text-gray-600">Tema</label>
                    <div class="flex items-center space-x-4">
                        <label><input type="radio" name="theme" value="light" checked> Claro</label>
                        <label><input type="radio" name="theme" value="dark"> Escuro</label>
                    </div>
                </div>
            </div>

            <!-- Seção de Idioma -->
            <div>
                <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4">Idioma</h2>
                <div class="flex items-center justify-between">
                    <label for="language" class="text-gray-600">Selecione o idioma</label>
                    <select id="language" name="language" class="border rounded-md px-3 py-2">
                        <option value="pt-br">Português (Brasil)</option>
                        <option value="en-us">English</option>
                        <option value="es">Español</option>
                    </select>
                </div>
            </div>

            <!-- Seção de Notificações -->
            <div>
                <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4">Notificações</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <label for="sound-notifications" class="text-gray-600">Notificações Sonoras</label>
                        <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                            <input type="checkbox" name="sound-notifications" id="sound-notifications" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" checked />
                            <label for="sound-notifications" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <label for="visual-notifications" class="text-gray-600">Notificações Visuais (Pop-ups)</label>
                        <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                            <input type="checkbox" name="visual-notifications" id="visual-notifications" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" checked />
                            <label for="visual-notifications" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção Sobre -->
            <div>
                <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4">Sobre</h2>
                <div class="flex flex-col space-y-2 text-blue-600">
                    <a href="#" class="hover:underline">Política de Privacidade</a>
                    <a href="#" class="hover:underline">Termos de Serviço</a>
                    <a href="#" class="hover:underline">Ajuda e Suporte</a>
                    <a href="#" class="hover:underline">Enviar Feedback</a>
                </div>
            </div>

            <!-- Botão de Salvar -->
            <div class="flex justify-end pt-4">
                <button id="save-settings-btn" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 transition duration-300">
                    Salvar Alterações
                </button>
            </div>

            <!-- Mensagem de feedback -->
            <div id="feedback-message" class="hidden text-center p-4 mt-4 rounded-md"></div>

        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Lógica de Aparência (Tema Claro/Escuro) ---
            const themeRadios = document.querySelectorAll('input[name="theme"]');
            themeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'dark') {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                });
            });

            // --- Lógica de Tamanho da Fonte ---
            const fontBtns = document.querySelectorAll('.font-btn');
            let selectedFontSize = 'text-base'; // Valor padrão
            fontBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    fontBtns.forEach(b => b.classList.remove('bg-blue-100', 'text-blue-600'));
                    this.classList.add('bg-blue-100', 'text-blue-600');
                    selectedFontSize = this.dataset.fontSize;
                    document.body.className = `bg-gray-50 ${selectedFontSize}`;
                });
            });

            // --- Lógica para Salvar as Configurações ---
            const saveBtn = document.getElementById('save-settings-btn');
            const feedbackMessage = document.getElementById('feedback-message');

            saveBtn.addEventListener('click', function() {
                // 1. Coleta todos os dados do formulário
                const settings = {
                    accessibility: {
                        narrator: document.getElementById('narrador').checked,
                        fontSize: selectedFontSize
                    },
                    appearance: {
                        theme: document.querySelector('input[name="theme"]:checked').value
                    },
                    language: document.getElementById('language').value,
                    notifications: {
                        sound: document.getElementById('sound-notifications').checked,
                        visual: document.getElementById('visual-notifications').checked
                    }
                };

                this.textContent = 'Salvando...';
                this.disabled = true;

                // 2. Envio para o backend com o URL CORRETO.
                // O caminho relativo '../../' sobe dois níveis de diretório (de /view/configuracao/ para a raiz)
                // e então entra em /controllers/.
                fetch('../../controllers/ConfiguracaoController.php?action=salvar', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(settings)
                    })
                    .then(response => {
                        // Verifica se a resposta da rede foi bem-sucedida
                        if (!response.ok) {
                            // Se o status for 404, 500 etc., lança um erro para ser pego pelo .catch()
                            throw new Error(`Erro na rede: ${response.statusText}`);
                        }
                        // Converte a resposta em JSON
                        return response.json();
                    })
                    .then(data => {
                        // Verifica o conteúdo da resposta JSON do seu controller
                        if (data.success) {
                            feedbackMessage.textContent = data.message;
                            feedbackMessage.className = 'text-center p-4 mt-4 rounded-md bg-green-100 text-green-700';
                        } else {
                            // Se success for false, trata como um erro
                            throw new Error(data.message || 'Ocorreu um erro ao salvar.');
                        }
                    })
                    .catch(error => {
                        // Mostra feedback de erro para qualquer falha na cadeia de promises
                        console.error('Erro:', error);
                        feedbackMessage.textContent = error.message || 'Ocorreu um erro. Tente novamente.';
                        feedbackMessage.className = 'text-center p-4 mt-4 rounded-md bg-red-100 text-red-700';
                    })
                    .finally(() => {
                        // Este bloco sempre será executado, seja em caso de sucesso ou erro
                        // Restaura o botão após um tempo
                        setTimeout(() => {
                            saveBtn.textContent = 'Salvar Alterações';
                            saveBtn.disabled = false;
                            feedbackMessage.classList.add('hidden');
                        }, 3000);
                    });
            });
        });
    </script>
</body>

</html>