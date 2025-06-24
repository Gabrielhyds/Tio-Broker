<!DOCTYPE html>
<!-- O elemento raiz da página, com o atributo 'lang' definindo o idioma como português do Brasil. -->
<html lang="pt-BR">

<head>
    <!-- Define o conjunto de caracteres como UTF-8, que suporta a maioria dos caracteres e símbolos. -->
    <meta charset="UTF-8" />
    <!-- Configura a viewport para garantir que a página seja renderizada corretamente em diferentes dispositivos. -->
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Define o título que aparecerá na aba do navegador. -->
    <title>Acesso Bloqueado</title>
    <!-- Inicia a seção de estilos CSS para a página. -->
    <style>
        /* Estilos aplicados ao corpo (body) da página. */
        body {
            margin: 0;
            /* Remove a margem padrão do navegador. */
            padding: 0;
            /* Remove o preenchimento padrão. */
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            /* Define a família de fontes. */
            background: #f0f2f5;
            /* Define uma cor de fundo cinza claro. */
            display: flex;
            /* Ativa o layout flexbox para alinhamento. */
            align-items: center;
            /* Centraliza o conteúdo verticalmente. */
            justify-content: center;
            /* Centraliza o conteúdo horizontalmente. */
            height: 100vh;
            /* Define a altura para 100% da altura da tela. */
        }

        /* Estilos para a caixa de mensagem. */
        .box {
            background: #fff;
            /* Fundo branco. */
            border-radius: 8px;
            /* Bordas arredondadas. */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            /* Adiciona uma sombra sutil. */
            max-width: 400px;
            /* Largura máxima da caixa. */
            width: 90%;
            /* Largura responsiva para telas menores. */
            padding: 30px 20px;
            /* Espaçamento interno. */
            text-align: center;
            /* Alinha todo o texto ao centro. */
        }

        /* Estilos para o título principal (h1) dentro da caixa. */
        .box h1 {
            font-size: 2rem;
            /* Tamanho da fonte. */
            color: #333;
            /* Cor do texto cinza escuro. */
            margin-bottom: 10px;
            /* Margem inferior. */
        }

        /* Estilos para os parágrafos (p) dentro da caixa. */
        .box p {
            color: #555;
            /* Cor cinza para o texto do parágrafo. */
            line-height: 1.5;
            /* Espaçamento entre as linhas para melhor legibilidade. */
        }

        /* Estilos para o contêiner dos emojis. */
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
            /* Margem superior para espaçamento. */
            padding: 10px 18px;
            /* Espaçamento interno para criar a aparência de botão. */
            background: #4b7bec;
            /* Cor de fundo azul. */
            color: #fff;
            /* Cor do texto branca. */
            text-decoration: none;
            /* Remove o sublinhado padrão do link. */
            border-radius: 4px;
            /* Bordas levemente arredondadas. */
            transition: background 0.2s ease-in-out;
            /* Efeito de transição suave na cor de fundo ao passar o mouse. */
        }

        /* Efeito ao passar o mouse sobre o link (botão). */
        .box a:hover {
            background: #3a6fd1;
            /* Tom de azul um pouco mais escuro. */
        }
    </style>
</head>
<!-- Corpo da página, onde o conteúdo visível é colocado. -->

<body>
    <!-- A caixa (div) que contém a mensagem de acesso bloqueado. -->
    <div class="box">
        <!-- Elemento span para exibir os emojis. -->
        <span class="emoji">🤷‍♂️🚫</span>
        <!-- O título principal da mensagem. -->
        <h1>Eita, ainda não rolou!</h1>
        <!-- Primeiro parágrafo explicando o motivo do bloqueio. -->
        <p>
            Parece que você ainda não está vinculado a nenhuma imobiliária.<br>
            Sem essa conexão, não dá pra seguir adiante.
        </p>
        <!-- Segundo parágrafo com a instrução para o usuário. -->
        <p>
            Fale com o administrador para liberar seu acesso e<br>
            curtir todas as funcionalidades do sistema!
        </p>
        <!-- Link (estilizado como botão) que leva o usuário de volta para a página de login. -->
        <a href="login.php">
            Voltar ao Login
        </a>
    </div>
</body>

</html>