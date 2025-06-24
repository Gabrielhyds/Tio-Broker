<!-- Declara o tipo de documento como HTML5, a versão mais recente do HTML. -->
<!DOCTYPE html>
<!-- O elemento raiz da página, com o atributo 'lang' definindo o idioma como português do Brasil. -->
<html lang="pt-BR">

<head>
    <!-- Define o conjunto de caracteres como UTF-8, que suporta a maioria dos caracteres e símbolos. -->
    <meta charset="UTF-8" />
    <!-- Configura a viewport para garantir que a página seja renderizada corretamente em diferentes tamanhos de tela (design responsivo). -->
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Define o título que aparecerá na aba do navegador. -->
    <title>Login Inválido</title>
    <!-- Inicia a seção de estilos CSS para a página. -->
    <style>
        /* Estilos aplicados ao corpo (body) da página. */
        body {
            margin: 0;
            /* Remove a margem padrão. */
            padding: 0;
            /* Remove o preenchimento padrão. */
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            /* Define a família de fontes. */
            background: #f0f2f5;
            /* Define uma cor de fundo cinza claro. */
            display: flex;
            /* Ativa o layout flexbox. */
            align-items: center;
            /* Centraliza o conteúdo verticalmente. */
            justify-content: center;
            /* Centraliza o conteúdo horizontalmente. */
            height: 100vh;
            /* Define a altura para 100% da altura da tela. */
        }

        /* Estilos para a caixa de mensagem de erro. */
        .box {
            background: #fff;
            /* Fundo branco. */
            border-radius: 8px;
            /* Bordas arredondadas. */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            /* Sombra sutil. */
            max-width: 400px;
            /* Largura máxima. */
            width: 90%;
            /* Largura responsiva. */
            padding: 30px 20px;
            /* Espaçamento interno. */
            text-align: center;
            /* Alinha o texto ao centro. */
        }

        /* Estilos para o título principal (h1) dentro da caixa. */
        .box h1 {
            font-size: 2rem;
            /* Tamanho da fonte. */
            color: #c0392b;
            /* Cor vermelha para indicar erro. */
            margin-bottom: 10px;
            /* Margem inferior. */
        }

        /* Estilos para os parágrafos (p) dentro da caixa. */
        .box p {
            color: #555;
            /* Cor cinza escuro para o texto. */
            line-height: 1.5;
            /* Espaçamento entre as linhas. */
        }

        /* Estilos para os emojis. */
        .box .emoji {
            font-size: 4rem;
            /* Tamanho grande para destaque. */
            margin-bottom: 15px;
            /* Margem inferior. */
            display: block;
            /* Garante que ocupe sua própria linha. */
        }

        /* Estilos para o link (botão) de "Voltar". */
        .box a {
            display: inline-block;
            /* Permite definir padding e margin. */
            margin-top: 20px;
            /* Margem superior. */
            padding: 10px 18px;
            /* Espaçamento interno. */
            background: #4b7bec;
            /* Cor de fundo azul. */
            color: #fff;
            /* Cor do texto branca. */
            text-decoration: none;
            /* Remove o sublinhado padrão do link. */
            border-radius: 4px;
            /* Bordas levemente arredondadas. */
            transition: background 0.2s ease-in-out;
            /* Efeito de transição suave na cor de fundo. */
        }

        /* Efeito ao passar o mouse sobre o link (botão). */
        .box a:hover {
            background: #3a6fd1;
            /* Tom de azul mais escuro. */
        }
    </style>
</head>
<!-- Corpo da página, onde o conteúdo visível é colocado. -->

<body>
    <!-- A caixa (div) que contém a mensagem de erro. -->
    <div class="box">
        <!-- Elemento span para exibir os emojis de erro. -->
        <span class="emoji">❌😕</span>
        <!-- O título principal da mensagem de erro. -->
        <h1>Ops! Credenciais Inválidas</h1>
        <!-- O parágrafo com a descrição do erro para o usuário. -->
        <p>
            O e-mail ou a senha inseridos não conferem com nossos registros.<br>
            Por favor, tente novamente.
        </p>
        <!-- O link (estilizado como botão) que leva o usuário de volta para a página de login. -->
        <a href="login.php">
            Voltar ao Login
        </a>
    </div>
</body>

</html>