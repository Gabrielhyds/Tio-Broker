<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Inválido</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .box {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 90%;
            padding: 30px 20px;
            text-align: center;
        }
        .box h1 {
            font-size: 2rem;
            color: #c0392b; /* Vermelho suave para erro */
            margin-bottom: 10px;
        }
        .box p {
            color: #555;
            line-height: 1.5;
        }
        .box .emoji {
            font-size: 4rem;
            margin-bottom: 15px;
            display: block;
        }
        .box a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 18px;
            background: #4b7bec;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.2s ease-in-out;
        }
        .box a:hover {
            background: #3a6fd1;
        }
    </style>
</head>
<body>
    <div class="box">
        <span class="emoji">❌😕</span>
        <h1>Ops! Credenciais Inválidas</h1>
        <p>
            O e-mail ou a senha inseridos não conferem com nossos registros.<br>
            Por favor, tente novamente.
        </p>
        <a href="login.php">
            Voltar ao Login
        </a>
    </div>
</body>
</html>
