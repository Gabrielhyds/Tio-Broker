<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Recuperar Senha - TioBroker</title>

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

    <div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-md text-center">

        <!-- Logo -->
        <div class="flex justify-center mb-4">
            <img src="../assets/img/tio_broker_ligth.png" alt="Logo Imobiliária" class="h-14">
        </div>

        <!-- Título -->
        <h4 class="text-xl font-semibold text-gray-800 mb-2">Redefinir Senha</h4>
        <p class="text-sm text-gray-600 mb-5">
            Informe seu e-mail cadastrado e enviaremos um link para redefinir sua senha.
        </p>

        <!-- Formulário -->
        <form action="../../controllers/ResetSenhaController.php" method="POST" autocomplete="off" class="space-y-4 text-left">

            <!-- E-mail -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="email">E-mail</label>
                <div class="flex items-center border border-gray-300 rounded-lg shadow-sm overflow-hidden">
                    <span class="px-3 bg-blue-50 text-blue-600"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" id="email" required placeholder="seu@email.com"
                        class="w-full py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Botão -->
            <button type="submit"
                class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-paper-plane mr-1"></i> Enviar link de redefinição
            </button>

            <!-- Voltar -->
            <div class="text-center mt-3">
                <a href="login.php" class="text-sm text-blue-600 hover:underline">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar ao login
                </a>
            </div>
        </form>
    </div>

</body>

</html>