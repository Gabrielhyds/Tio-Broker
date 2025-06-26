<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login - Tio Broker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-600 to-sky-800 min-h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">

        <div class="flex flex-col items-center mb-6">
            <img src="../assets/img/tio_broker_ligth.png" alt="Logo Imobiliária" class="h-14 mb-2">
            <h2 class="text-xl font-semibold text-gray-800">Acesso ao Painel</h2>
        </div>

        <!-- Mensagem de sucesso após redefinição -->
        <?php if (isset($_GET['reset']) && $_GET['reset'] === 'success'): ?>
            <div class="bg-green-100 text-green-800 p-3 rounded-md mb-4 text-sm">
                Senha redefinida com sucesso! Faça login com sua nova senha.
            </div>
        <?php endif; ?>

        <form action="../../controllers/AuthController.php" method="POST" autocomplete="off" class="space-y-5">
            <input type="hidden" name="action" value="login">

            <!-- E-mail -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                <div class="flex items-center bg-gray-50 border border-gray-300 rounded-lg overflow-hidden">
                    <span class="px-3 text-blue-600"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" required class="w-full p-2 bg-transparent focus:outline-none focus:ring-0" placeholder="seu@email.com">
                </div>
            </div>

            <!-- Senha -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                <div class="flex items-center bg-gray-50 border border-gray-300 rounded-lg overflow-hidden">
                    <span class="px-3 text-blue-600"><i class="fas fa-lock"></i></span>
                    <input type="password" name="senha" required class="w-full p-2 bg-transparent focus:outline-none focus:ring-0" placeholder="Sua senha">
                </div>
            </div>

            <!-- Botão de login -->
            <div class="pt-2">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg shadow">
                    <i class="fas fa-sign-in-alt mr-1"></i> Entrar
                </button>
            </div>

            <!-- Link de recuperação -->
            <div class="text-center text-sm">
                <a href="recuperar_senha.php" class="text-blue-600 hover:underline">Esqueci minha senha</a>
            </div>
        </form>

    </div>

</body>

</html>