<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Acesso Bloqueado</title>
    <!-- Tailwind CSS + Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-600 to-sky-800 min-h-screen flex items-center justify-center px-4">

    <div class="bg-white max-w-md w-full p-8 rounded-xl shadow-lg text-center">

        <!-- Emojis -->
        <div class="text-5xl mb-4">🤷‍♂️🚫</div>

        <!-- Título -->
        <h1 class="text-2xl font-semibold text-gray-800 mb-3">Eita, ainda não rolou!</h1>

        <!-- Mensagem explicativa -->
        <p class="text-gray-600 text-sm leading-relaxed mb-4">
            Parece que você ainda não está vinculado a nenhuma imobiliária.<br>
            Sem essa conexão, não dá pra seguir adiante.
        </p>
        <p class="text-gray-600 text-sm leading-relaxed">
            Fale com o administrador para liberar seu acesso e<br>
            curtir todas as funcionalidades do sistema!
        </p>

        <!-- Botão de voltar -->
        <a href="login.php"
            class="inline-block mt-6 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
            <i class="fas fa-arrow-left mr-1"></i> Voltar ao Login
        </a>
    </div>

</body>

</html>