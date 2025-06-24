<?php
// Define que esta é a tela para solicitar o link de recuperação de senha.
?>
<!-- Declara o tipo de documento como HTML5. -->
<!DOCTYPE html>
<!-- O elemento raiz da página, com o idioma definido como português do Brasil. -->
<html lang="pt-br">

<head>
    <!-- Define o conjunto de caracteres como UTF-8. -->
    <meta charset="UTF-8">
    <!-- Define o título que aparecerá na aba do navegador. -->
    <title>Recuperar Senha - Portal Imobiliário</title>
    <!-- Importa a folha de estilos do Bootstrap via CDN. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Inicia a seção de estilos CSS customizados para a página. -->
    <style>
        /* Estilos aplicados ao corpo da página. */
        body {
            /* Cria um fundo com gradiente linear para um visual moderno. */
            background: linear-gradient(120deg, #1379df 60%, #0d375f 100%);
            min-height: 100vh;
            /* Garante que o corpo ocupe a altura total da tela. */
            display: flex;
            /* Ativa o layout flexbox para centralização. */
            align-items: center;
            /* Alinha o conteúdo verticalmente ao centro. */
            justify-content: center;
            /* Alinha o conteúdo horizontalmente ao centro. */
            font-family: 'Segoe UI', Arial, sans-serif;
            /* Define a família de fontes. */
        }

        /* Estilos para a caixa principal da tela de recuperação. */
        .reset-box {
            background: #fff;
            /* Fundo branco. */
            padding: 2.5rem 2rem 2rem 2rem;
            /* Espaçamento interno. */
            border-radius: 1.2rem;
            /* Bordas bem arredondadas. */
            /* Adiciona uma sombra sutil para dar profundidade. */
            box-shadow: 0 8px 40px rgba(19, 121, 223, 0.15), 0 1.5px 6px rgba(0, 0, 0, 0.04);
            max-width: 410px;
            /* Largura máxima. */
            width: 100%;
            /* Largura responsiva. */
            margin: auto;
            /* Garante a centralização. */
        }

        /* Estilos para o contêiner do logo. */
        .reset-logo {
            display: flex;
            /* Ativa o flexbox. */
            flex-direction: column;
            /* Organiza os itens em coluna. */
            align-items: center;
            /* Alinha ao centro horizontalmente. */
            justify-content: center;
            /* Alinha ao centro verticalmente. */
            margin-bottom: 1.2rem;
            /* Margem inferior. */
        }

        /* Estilos para a imagem do logo. */
        .reset-logo img {
            height: 54px;
            /* Altura fixa. */
            width: auto;
            /* Largura automática para manter a proporção. */
            margin-bottom: 8px;
            /* Margem inferior. */
        }

        /* Estilos para o texto do logo (se houver). */
        .reset-logo span {
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

        /* Estilo para o campo de input quando em foco. */
        .form-control:focus {
            border-color: #1379df;
            /* Muda a cor da borda. */
            box-shadow: 0 0 0 0.2rem rgba(19, 121, 223, .09);
            /* Adiciona uma sombra suave. */
        }

        /* Estilos para o botão principal. */
        .btn-primary {
            background: linear-gradient(90deg, #1379df, #1b8bff 85%);
            /* Fundo com gradiente. */
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
            background: linear-gradient(90deg, #0d375f, #1379df 90%);
            /* Muda o gradiente. */
        }

        /* Estilos para o rodapé da caixa. */
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
    <!-- A caixa principal da tela de recuperação de senha. -->
    <div class="reset-box shadow">
        <!-- O logo da empresa. -->
        <div class="reset-logo mb-3 flex-column">
            <img src="../assets/img/tio_broker_ligth.png" alt="Logo Imobiliária">
        </div>
        <!-- Título da tela. -->
        <h4 class="mb-2 text-center" style="letter-spacing:.5px;color:#09304d;">Redefinir Senha</h4>
        <!-- Texto instrutivo para o usuário. -->
        <p class="mb-4 text-center text-muted small">
            Informe seu e-mail cadastrado e enviaremos um link para redefinir sua senha.
        </p>
        <!-- Início do formulário de recuperação. -->
        <form action="../../controllers/ResetSenhaController.php" method="POST" autocomplete="off">
            <!-- Campo de E-mail. -->
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <!-- Grupo de input para combinar ícone e campo de texto. -->
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="seu@email.com" required>
                </div>
            </div>
            <!-- Botão de envio do formulário. -->
            <div class="d-grid mt-4 mb-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane me-1"></i> Enviar link de redefinição
                </button>
            </div>
            <!-- Link para voltar à página de login. -->
            <div class="mt-2 text-center">
                <a href="login.php" class="link-secondary small"><i class="fas fa-arrow-left me-1"></i>Voltar ao login</a>
            </div>
        </form>
    </div>
    <!-- Script do Bootstrap (bundle) via CDN. -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script do Font Awesome via CDN para carregar os ícones. -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>

</html>