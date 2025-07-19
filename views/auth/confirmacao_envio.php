<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Link Enviado - Tio Broker CRM</title>
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
        <div class="flex w-full max-w-5xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-blue-700 to-blue-900 p-12 text-white flex-col justify-between relative overflow-hidden opacity-0 animate-fade-in">
                <div class="absolute inset-0 flex items-center justify-center"><i class="fas fa-house-chimney text-white opacity-10 text-[18rem] transform -rotate-12"></i></div>
                <div class="relative z-10">
                    <img src="../assets/img/tio_broker_dark.png" alt="Logo Tio Broker" class="h-12 w-auto mb-8" onerror="this.onerror=null;this.src='https://placehold.co/200x50/ffffff/FFFFFF?text=Tio+Broker&font=inter';">
                    <h1 class="text-3xl font-bold tracking-tight">Tudo certo!</h1>
                    <p class="mt-4 text-blue-200">O próximo passo para recuperar seu acesso está na sua caixa de entrada.</p>
                </div>
                <div class="relative z-10 text-sm text-blue-300">&copy; <?= date('Y'); ?> Tio Broker.</div>
            </div>
            <div class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-center items-center text-center">
                <div class="text-green-500 mb-4 opacity-0 animate-fade-in-up" style="animation-delay: 0.1s;"><i class="fas fa-paper-plane fa-4x"></i></div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2 opacity-0 animate-fade-in-up" style="animation-delay: 0.2s;">Link Enviado!</h2>
                <p class="text-gray-600 mb-8 max-w-sm opacity-0 animate-fade-in-up" style="animation-delay: 0.3s;">Se o e-mail informado estiver em nosso sistema, enviaremos um link de redefinição. Por favor, verifique sua caixa de entrada e também a pasta de spam.</p>
                <div class="opacity-0 animate-fade-in-up" style="animation-delay: 0.4s;">
                    <a href="login.php" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 ease-in-out transform hover:-translate-y-0.5"><i class="fas fa-arrow-left mr-2"></i> Voltar ao Login</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>