<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: login.php (VERSÃO FINAL)
|--------------------------------------------------------------------------
*/
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title data-i18n="login.title">Login - Tio Broker CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon.png">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }

        .animate-fade-in {
            animation: fadeIn .5s ease-out forwards
        }

        .animate-fade-in-up {
            animation: fadeInUp .5s ease-out forwards
        }

        .bg-login {
            position: relative;
            overflow: hidden
        }

        .bg-login::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            z-index: -1;
            background-image: linear-gradient(to bottom, rgba(0, 0, 0, .6), rgba(0, 0, 0, .8)), url(../assets/img/login.png);
            background-size: cover;
            background-position: center;
            filter: blur(8px)
        }

        .translating {
            visibility: hidden;
        }
    </style>
</head>

<body class="bg-gray-100 bg-login">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="flex w-full max-w-5xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-blue-700 to-blue-900 p-12 text-white flex-col justify-between relative overflow-hidden opacity-0 animate-fade-in">
                <div class="absolute inset-0 flex items-center justify-center"><i class="fas fa-house-chimney text-white opacity-10 text-[18rem] transform -rotate-12"></i></div>
                <div class="relative z-10">
                    <img src="../assets/img/tio_broker_dark.png" alt="Logo Tio Broker" class="h-12 w-auto mb-8" onerror="this.onerror=null;this.src='https://placehold.co/200x50/ffffff/FFFFFF?text=Tio+Broker&font=inter';">
                    <h1 class="text-3xl font-bold tracking-tight translating" data-i18n="login.mainHeading">Potencialize sua imobiliária.</h1>
                    <p class="mt-4 text-blue-200 translating" data-i18n="login.subheading">Acesse a plataforma completa para gerenciar seus clientes, imóveis e negociações com eficiência.</p>
                </div>
                <div class="relative z-10 text-sm text-blue-300">&copy; <?php echo date('Y'); ?> <span class="translating" data-i18n="common.copyright">Tio Broker.</span></div>
            </div>
            <div class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-center">
                <div class="md:hidden text-center mb-8 opacity-0 animate-fade-in-up">
                    <img src="../assets/img/tio_broker_ligth.png" alt="Logo Tio Broker" class="h-10 mx-auto" onerror="this.onerror=null;this.src='https://placehold.co/150x40?text=Tio+Broker';">
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2 opacity-0 animate-fade-in-up translating" style="animation-delay: 0.1s;" data-i18n="login.formTitle">Acesso ao Painel</h2>
                <p class="text-gray-600 mb-8 opacity-0 animate-fade-in-up translating" style="animation-delay: 0.2s;" data-i18n="login.welcome">Bem-vindo de volta! Por favor, insira seus dados.</p>

                <?php if (isset($_GET['reset']) && $_GET['reset'] === 'success'): ?>
                    <div class="bg-green-100 text-green-800 p-3 rounded-lg mb-6 text-center text-sm font-medium translating" data-i18n="login.passwordResetSuccess">
                        Senha redefinida com sucesso! Faça login com sua nova senha.
                    </div>
                <?php endif; ?>

                <form action="../../controllers/AuthController.php" method="POST" autocomplete="off" class="space-y-6">
                    <input type="hidden" name="action" value="login">
                    <div class="opacity-0 animate-fade-in-up" style="animation-delay: 0.3s;">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1 translating" data-i18n="common.emailLabel">E-mail</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3"><i class="fas fa-envelope text-gray-400"></i></span>
                            <input id="email" type="email" name="email" required class="w-full p-3 pl-10 bg-gray-50 border border-gray-300 rounded-lg placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500" data-i18n-placeholder="common.emailPlaceholder" placeholder="seu@email.com">
                        </div>
                    </div>
                    <div class="opacity-0 animate-fade-in-up" style="animation-delay: 0.4s;">
                        <label for="senha" class="block text-sm font-medium text-gray-700 mb-1 translating" data-i18n="common.passwordLabel">Senha</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3"><i class="fas fa-lock text-gray-400"></i></span>
                            <input id="senha" type="password" name="senha" required class="w-full p-3 pl-10 pr-10 bg-gray-50 border border-gray-300 rounded-lg placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500" data-i18n-placeholder="common.passwordPlaceholder" placeholder="Sua senha">
                            <span id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-gray-400 hover:text-gray-600"><i class="fas fa-eye"></i></span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between opacity-0 animate-fade-in-up" style="animation-delay: 0.5s;">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-700 translating" data-i18n="login.rememberMe">Lembrar-me</label>
                        </div>
                        <div class="text-sm">
                            <a href="recuperar_senha.php" class="font-medium text-blue-600 hover:text-blue-500 translating" data-i18n="login.forgotPassword">Esqueci minha senha</a>
                        </div>
                    </div>
                    <div class="opacity-0 animate-fade-in-up" style="animation-delay: 0.6s;">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md transition-all duration-300 ease-in-out transform hover:-translate-y-0.5">
                            <i class="fas fa-sign-in-alt mr-2"></i> <span class="translating" data-i18n="login.button">Entrar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('senha');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        (async function() {
            let translations = {};
            async function loadTranslations(lang) {
                try {
                    const response = await fetch(`../../controllers/TraducaoController.php?modulo=auth&lang=${lang}`);
                    const result = await response.json();
                    if (result.success) translations = result.data;
                } catch (error) {
                    console.error('Failed to load translations:', error);
                }
            }

            function t(key) {
                return key.split('.').reduce((o, i) => o && o[i], translations) || key;
            }

            function applyTranslations(lang) {
                document.documentElement.lang = lang;
                document.querySelectorAll('[data-i18n]').forEach(el => {
                    const key = el.dataset.i18n;
                    const translation = t(key);
                    if (translation !== key) {
                        el.innerText = translation;
                    }
                    el.classList.remove('translating');
                });
                document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
                    el.placeholder = t(el.dataset.i18nPlaceholder);
                });
            }

            function getInitialLang() {
                const savedLang = localStorage.getItem('calendarLang');
                if (savedLang && ['pt-br', 'en', 'es'].includes(savedLang)) {
                    return savedLang;
                }
                const browserLang = navigator.language.toLowerCase();
                if (browserLang.startsWith('es')) return 'es';
                if (browserLang.startsWith('en')) return 'en';
                return 'pt-br';
            }

            const initialLang = getInitialLang();
            localStorage.setItem('calendarLang', initialLang);
            await loadTranslations(initialLang);
            applyTranslations(initialLang);
        })();
    </script>
</body>

</html>

<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: recuperar_senha.php (VERSÃO FINAL)
|--------------------------------------------------------------------------
*/
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title data-i18n="recover.title">Recuperar Senha - Tio Broker CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }

        .animate-fade-in {
            animation: fadeIn .5s ease-out forwards
        }

        .animate-fade-in-up {
            animation: fadeInUp .5s ease-out forwards
        }

        .bg-login {
            position: relative;
            overflow: hidden
        }

        .bg-login::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            z-index: -1;
            background-image: linear-gradient(to bottom, rgba(0, 0, 0, .6), rgba(0, 0, 0, .8)), url(../assets/img/login.png);
            background-size: cover;
            background-position: center;
            filter: blur(8px)
        }

        .translating {
            visibility: hidden;
        }
    </style>
</head>

<body class="bg-gray-100 bg-login">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="flex w-full max-w-5xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-blue-700 to-blue-900 p-12 text-white flex-col justify-between relative overflow-hidden opacity-0 animate-fade-in">
                <div class="absolute inset-0 flex items-center justify-center"><i class="fas fa-house-chimney text-white opacity-10 text-[18rem] transform -rotate-12"></i></div>
                <div class="relative z-10">
                    <img src="../assets/img/tio_broker_dark.png" alt="Logo Tio Broker" class="h-12 w-auto mb-8" onerror="this.onerror=null;this.src='https://placehold.co/200x50/ffffff/FFFFFF?text=Tio+Broker&font=inter';">
                    <h1 class="text-3xl font-bold tracking-tight translating" data-i18n="recover.mainHeading">Não se preocupe, acontece!</h1>
                    <p class="mt-4 text-blue-200 translating" data-i18n="recover.subheading">Vamos te ajudar a recuperar o acesso à sua conta de forma rápida e segura.</p>
                </div>
                <div class="relative z-10 text-sm text-blue-300">&copy; <?= date('Y'); ?> <span class="translating" data-i18n="common.copyright">Tio Broker.</span></div>
            </div>
            <div class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-center">
                <h2 class="text-2xl font-bold text-gray-800 mb-2 opacity-0 animate-fade-in-up translating" style="animation-delay: 0.1s;" data-i18n="recover.formTitle">Redefinir Senha</h2>
                <p class="text-gray-600 mb-8 opacity-0 animate-fade-in-up translating" style="animation-delay: 0.2s;" data-i18n="recover.instructions">Informe seu e-mail cadastrado e enviaremos um link para criar uma nova senha.</p>
                <form action="../../controllers/ResetSenhaController.php" method="POST" autocomplete="off" class="space-y-6">
                    <div class="opacity-0 animate-fade-in-up" style="animation-delay: 0.3s;">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1 translating" data-i18n="common.emailLabel">E-mail</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3"><i class="fas fa-envelope text-gray-400"></i></span>
                            <input id="email" type="email" name="email" required class="w-full p-3 pl-10 bg-gray-50 border border-gray-300 rounded-lg placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500" data-i18n-placeholder="common.emailPlaceholder" placeholder="seu@email.com">
                        </div>
                    </div>
                    <div class="opacity-0 animate-fade-in-up" style="animation-delay: 0.4s;">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md transition-all duration-300">
                            <i class="fas fa-paper-plane mr-2"></i> <span class="translating" data-i18n="recover.button">Enviar Link de Redefinição</span>
                        </button>
                    </div>
                    <div class="text-center opacity-0 animate-fade-in-up" style="animation-delay: 0.5s;">
                        <a href="login.php" class="text-sm font-medium text-blue-600 hover:text-blue-500"><i class="fas fa-arrow-left mr-1"></i> <span class="translating" data-i18n="common.backToLogin">Voltar ao Login</span></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        (async function() {
            let translations = {};
            async function loadTranslations(lang) {
                try {
                    const response = await fetch(`../../controllers/TraducaoController.php?modulo=auth&lang=${lang}`);
                    const result = await response.json();
                    if (result.success) translations = result.data;
                } catch (error) {
                    console.error('Failed to load translations:', error);
                }
            }

            function t(key) {
                return key.split('.').reduce((o, i) => o && o[i], translations) || key;
            }

            function applyTranslations(lang) {
                document.documentElement.lang = lang;
                document.querySelectorAll('[data-i18n]').forEach(el => {
                    el.innerText = t(el.dataset.i18n);
                    el.classList.remove('translating');
                });
                document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
                    el.placeholder = t(el.dataset.i18nPlaceholder);
                });
            }

            function getInitialLang() {
                const savedLang = localStorage.getItem('calendarLang');
                if (savedLang && ['pt-br', 'en', 'es'].includes(savedLang)) {
                    return savedLang;
                }
                const browserLang = navigator.language.toLowerCase();
                if (browserLang.startsWith('es')) return 'es';
                if (browserLang.startsWith('en')) return 'en';
                return 'pt-br';
            }

            const initialLang = getInitialLang();
            await loadTranslations(initialLang);
            applyTranslations(initialLang);
        })();
    </script>
</body>

</html>