<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Inválido - TioBroker</title>

    <!-- Tailwind CSS CDN + Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-600 to-sky-800 min-h-screen flex items-center justify-center px-4">

    <div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-md text-center">

        <!-- Emoji -->
        <div class="text-5xl mb-4 text-red-500">❌😕</div>

        <!-- Título -->
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Ops! Credenciais Inválidas</h1>

        <!-- Mensagem -->
        <p class="text-gray-600 text-sm leading-relaxed">
            O e-mail ou a senha inseridos não conferem com nossos registros.<br>
            Por favor, tente novamente.
        </p>

        <!-- Botão de voltar -->
        <a href="login.php" class="inline-block mt-6 px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">
            <i class="fas fa-arrow-left mr-2"></i> Voltar ao Login
        </a>

    </div>

</body>

</html>