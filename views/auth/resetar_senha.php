<?php
$token = $_GET['token'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Definir Nova Senha</title>
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

    <div class="bg-white w-full max-w-md p-8 rounded-xl shadow-lg">

        <!-- Título -->
        <h2 class="text-center text-2xl font-semibold text-blue-700 mb-4">Nova Senha</h2>

        <!-- Mensagens -->
        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
                Token inválido ou expirado. Solicite novamente.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4 text-sm">
                Enviamos o link para seu e-mail!
            </div>
        <?php endif; ?>

        <!-- Formulário -->
        <form action="../../controllers/ResetSenhaController.php" method="POST" class="space-y-4">

            <!-- Token oculto -->
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <!-- Nova senha -->
            <div>
                <label for="senha" class="block text-sm font-medium text-gray-700">Nova senha</label>
                <input type="password" name="senha" id="senha" required
                    class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Botão -->
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-medium transition">
                Redefinir senha
            </button>
        </form>

        <!-- Voltar ao login -->
        <div class="text-center mt-4">
            <a href="login.php" class="text-sm text-blue-600 hover:underline">
                <i class="fas fa-arrow-left mr-1"></i>Voltar ao login
            </a>
        </div>
    </div>

</body>

</html>