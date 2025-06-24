<?php
// Define que esta é uma tela de login estilizada para o portal imobiliário.
?>
<!-- Declara o tipo de documento como HTML5. -->
<!DOCTYPE html>
<!-- O elemento raiz da página, com o idioma definido como português do Brasil. -->
<html lang="pt-br">

<head>
    <!-- Define o conjunto de caracteres como UTF-8. -->
    <meta charset="UTF-8">
    <!-- Define o título que aparecerá na aba do navegador. -->
    <title>Login - Portal Imobiliário</title>
    <!-- Importa a folha de estilos do Bootstrap via CDN para estilização rápida. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Importa a biblioteca Font Awesome para usar ícones (ex: envelope, cadeado). -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Inicia a seção de estilos CSS customizados para a página. -->
    <style>
        /* Estilos para o corpo da página. */
        body {
            /* Cria um fundo com gradiente linear. */
            background: linear-gradient(120deg, #1379df 60%, #0d375f 100%);
            min-height: 100vh;
            /* Garante que o corpo ocupe a altura total da tela. */
            display: flex;
            /* Ativa o layout flexbox. */
            align-items: center;
            /* Centraliza o conteúdo verticalmente. */
            justify-content: center;
            /* Centraliza o conteúdo horizontalmente. */
            font-family: 'Segoe UI', Arial, sans-serif;
            /* Define a família de fontes. */
        }

        /* Estilos para a caixa de login. */
        .login-box {
            background: #fff;
            /* Fundo branco. */
            padding: 2.5rem 2rem 2rem 2rem;
            /* Espaçamento interno. */
            border-radius: 1.2rem;
            /* Bordas bem arredondadas. */
            /* Adiciona duas camadas de sombra para um efeito de profundidade. */
            box-shadow: 0 8px 40px rgba(19, 121, 223, 0.15), 0 1.5px 6px rgba(0, 0, 0, 0.04);
            max-width: 410px;
            /* Largura máxima da caixa. */
            width: 100%;
            /* Largura responsiva. */
            margin: auto;
            /* Centraliza a caixa horizontalmente (extra, caso o flex não se aplique). */
            position: relative;
            /* Posição relativa para elementos internos. */
        }

        /* Estilos para o contêiner do logo. */
        .login-logo {
            display: flex;
            /* Ativa o flexbox. */
            align-items: center;
            /* Alinha itens verticalmente. */
            justify-content: center;
            /* Centraliza itens horizontalmente. */
            margin-bottom: 1.2rem;
            /* Margem inferior. */
        }

        /* Estilos para o ícone do logo. */
        .login-logo i {
            font-size: 2.2rem;
            /* Tamanho do ícone. */
            color: #1379df;
            /* Cor do ícone. */
            margin-right: 0.5rem;
            /* Margem à direita. */
        }

        /* Estilos para o texto do logo. */
        .login-logo span {
            font-size: 1.6rem;
            /* Tamanho da fonte. */
            font-weight: bold;
            /* Texto em negrito. */
            color: #0d375f;
            /* Cor do texto. */
        }

        /* Estilos para os rótulos (labels) do formulário. */
        .form-label {
            font-weight: 500;
            /* Peso da fonte. */
            color: #0d375f;
            /* Cor do texto. */
        }

        /* Estilos para os campos de input do formulário. */
        .form-control {
            border-radius: 0.7rem;
            /* Bordas arredondadas. */
            border: 1px solid #d5e2f6;
            /* Cor da borda. */
        }

        /* Estilos para o campo de input quando está em foco. */
        .form-control:focus {
            border-color: #1379df;
            /* Muda a cor da borda. */
            /* Adiciona uma sombra suave para indicar foco. */
            box-shadow: 0 0 0 0.2rem rgba(19, 121, 223, .09);
        }

        /* Estilos para o ícone dentro do grupo de input. */
        .input-group-text {
            background: #eaf3fb;
            /* Cor de fundo suave. */
            border: none;
            /* Remove a borda. */
            border-radius: 0.7rem 0 0 0.7rem;
            /* Arredonda apenas os cantos esquerdos. */
            color: #1379df;
            /* Cor do ícone. */
        }

        /* Estilos para o botão primário (Login). */
        .btn-primary {
            /* Fundo com gradiente. */
            background: linear-gradient(90deg, #1379df, #1b8bff 85%);
            border: none;
            /* Remove a borda. */
            border-radius: 0.7rem;
            /* Bordas arredondadas. */
            font-weight: 500;
            /* Peso da fonte. */
            box-shadow: 0 3px 12px rgba(19, 121, 223, 0.09);
            /* Sombra suave. */
            transition: 0.18s;
            /* Efeito de transição suave. */
        }

        /* Efeito ao passar o mouse sobre o botão. */
        .btn-primary:hover {
            /* Muda o gradiente para um tom mais escuro. */
            background: linear-gradient(90deg, #0d375f, #1379df 90%);
        }

        /* Estilos para o rodapé da caixa de login. */
        .login-footer {
            margin-top: 2rem;
            /* Margem superior. */
            text-align: center;
            /* Alinha o texto ao centro. */
            font-size: .97rem;
            /* Tamanho da fonte. */
            color: #8da1be;
            /* Cor do texto. */
        }
    </style>
</head>

<body>

    <!-- A caixa de login principal com uma sombra. -->
    <div class="login-box shadow">
        <div class="login-logo mb-3 flex-column">
            <!-- Imagem do logo da imobiliária. -->
            <img src="../assets/img/tio_broker_ligth.png" alt="Logo Imobiliária" style="height: 54px; width: auto; margin-bottom: 8px;">
        </div>
        <!-- Título da caixa de login. -->
        <h3 class="mb-3 text-center" style="letter-spacing:.5px;color:#09304d;">Acesso ao Painel</h3>
        <!-- Bloco PHP para exibir uma mensagem de sucesso após a redefinição de senha. -->
        <?php if (isset($_GET['reset']) && $_GET['reset'] === 'success'): ?>
            <div class="alert alert-success">Senha redefinida com sucesso! Faça login com sua nova senha.</div>
        <?php endif; ?>

        <!-- Início do formulário de login. -->
        <form action="../../controllers/AuthController.php" method="POST" autocomplete="off">
            <!-- Campo oculto para indicar a ação que o controller deve executar. -->
            <input type="hidden" name="action" value="login">

            <!-- Campo de E-mail. -->
            <div class="mb-3">
                <label for="email" class="form-label" style="letter-spacing:.5px;color:#09304d;">E-mail</label>
                <!-- Grupo de input para combinar ícone e campo de texto. -->
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="seu@email.com" required>
                </div>
            </div>

            <!-- Campo de Senha. -->
            <div class="mb-3">
                <label for="senha" class="form-label" style="letter-spacing:.5px;color:#09304d;">Senha</label>
                <!-- Grupo de input com ícone de cadeado. -->
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="senha" class="form-control" placeholder="Sua senha" required>
                </div>
            </div>

            <!-- Botão de Login. -->
            <div class="d-grid mb-2 mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt me-1"></i> Entrar
                </button>
            </div>
            <!-- Link para recuperação de senha. -->
            <div class="mb-2 text-center">
                <a href="recuperar_senha.php" class="link-primary text-decoration-none small">
                    Esqueci minha senha?
                </a>
            </div>
        </form>
    </div>

    <!-- Script do Bootstrap (bundle) via CDN, necessário para funcionalidades interativas. -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>