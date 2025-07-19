<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login - Tio Broker CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome para ícones -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Define a fonte base para o corpo do documento */
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Animações de Entrada */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Classes utilitárias para aplicar as animações */
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
        }

        /* Estilo para o fundo com imagem e desfoque */
        .bg-login {
            position: relative;
            /* Necessário para o posicionamento do pseudo-elemento */
            overflow: hidden;
            /* Garante que o blur não vaze */
        }

        .bg-login::before {
            content: '';
            position: absolute;
            top: -10px;
            /* Um pouco maior para evitar bordas */
            left: -10px;
            right: -10px;
            bottom: -10px;
            z-index: -1;
            /* Coloca o pseudo-elemento atrás do conteúdo */

            background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8)),
                url('../assets/img/login.png');
            background-size: cover;
            background-position: center;

            /* Aplica o filtro de desfoque */
            filter: blur(8px);
        }
    </style>
</head>

<body class="bg-gray-100 bg-login">

    <div class="min-h-screen flex items-center justify-center p-4">

        <!-- Container principal com layout de duas colunas -->
        <div class="flex w-full max-w-5xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden">

            <!-- Coluna da Esquerda (Branding) - Visível em telas médias e maiores -->
            <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-blue-700 to-blue-900 p-12 text-white flex-col justify-between relative overflow-hidden opacity-0 animate-fade-in">
                <!-- Ícone de fundo decorativo -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-house-chimney text-white opacity-10 text-[18rem] transform -rotate-12"></i>
                </div>

                <!-- Conteúdo -->
                <div class="relative z-10">
                    <!-- Logo completa, com altura definida e largura automática para manter a proporção -->
                    <img src="../assets/img/tio_broker_dark.png" alt="Logo Tio Broker" class="h-12 w-auto mb-8" onerror="this.onerror=null;this.src='https://placehold.co/200x50/ffffff/FFFFFF?text=Tio+Broker&font=inter';">
                    <h1 class="text-3xl font-bold tracking-tight">Potencialize sua imobiliária.</h1>
                    <p class="mt-4 text-blue-200">Acesse a plataforma completa para gerenciar seus clientes, imóveis e negociações com eficiência.</p>
                </div>
                <div class="relative z-10 text-sm text-blue-300">
                    &copy; <?php echo date('Y'); ?> Tio Broker.
                </div>
            </div>

            <!-- Coluna da Direita (Formulário) -->
            <div class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-center">

                <!-- Logo para telas pequenas -->
                <div class="md:hidden text-center mb-8 opacity-0 animate-fade-in-up">
                    <img src="../assets/img/tio_broker_ligth.png" alt="Logo Tio Broker" class="h-10 mx-auto" onerror="this.onerror=null;this.src='https://placehold.co/150x40?text=Tio+Broker';">
                </div>

                <h2 class="text-2xl font-bold text-gray-800 mb-2 opacity-0 animate-fade-in-up" style="animation-delay: 0.1s;">Acesso ao Painel</h2>
                <p class="text-gray-600 mb-8 opacity-0 animate-fade-in-up" style="animation-delay: 0.2s;">Bem-vindo de volta! Por favor, insira seus dados.</p>

                <!-- Alerta de senha redefinida (lógica PHP mantida) -->
                <!-- 
                <?php if (isset($_GET['reset']) && $_GET['reset'] === 'success'): ?>
                    <div class="bg-green-100 text-green-800 p-3 rounded-lg mb-6 text-center text-sm font-medium">
                        Senha redefinida com sucesso! Faça login com sua nova senha.
                    </div>
                <?php endif; ?>
                -->

                <!-- Formulário -->
                <form action="../../controllers/AuthController.php" method="POST" autocomplete="off" class="space-y-6">
                    <input type="hidden" name="action" value="login">

                    <!-- Campo de E-mail -->
                    <div class="opacity-0 animate-fade-in-up" style="animation-delay: 0.3s;">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </span>
                            <input id="email" type="email" name="email" required class="w-full p-3 pl-10 bg-gray-50 border border-gray-300 rounded-lg placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300" placeholder="seu@email.com">
                        </div>
                    </div>

                    <!-- Campo de Senha -->
                    <div class="opacity-0 animate-fade-in-up" style="animation-delay: 0.4s;">
                        <label for="senha" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-lock text-gray-400"></i>
                            </span>
                            <input id="senha" type="password" name="senha" required class="w-full p-3 pl-10 pr-10 bg-gray-50 border border-gray-300 rounded-lg placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300" placeholder="Sua senha">
                            <span id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Opções Lembrar-me e Esqueci a senha -->
                    <div class="flex items-center justify-between opacity-0 animate-fade-in-up" style="animation-delay: 0.5s;">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-700">Lembrar-me</label>
                        </div>
                        <div class="text-sm">
                            <a href="recuperar_senha.php" class="font-medium text-blue-600 hover:text-blue-500">Esqueci minha senha</a>
                        </div>
                    </div>

                    <!-- Botão de Login -->
                    <div class="opacity-0 animate-fade-in-up" style="animation-delay: 0.6s;">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 ease-in-out transform hover:-translate-y-0.5">
                            <i class="fas fa-sign-in-alt mr-2"></i> Entrar
                        </button>
                    </div>

                </form>
            </div>

        </div>

    </div>

    <script>
        // Funcionalidade para mostrar/ocultar senha
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#senha');
        const passwordIcon = togglePassword.querySelector('i');

        togglePassword.addEventListener('click', function() {
            // Alterna o tipo do input
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Alterna o ícone do olho
            passwordIcon.classList.toggle('fa-eye');
            passwordIcon.classList.toggle('fa-eye-slash');
        });
    </script>

</body>

</html>