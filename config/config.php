<?php

// Função para conectar ao banco de dados automaticamente
function conectarBanco()
{
    // Define o host do banco de dados (geralmente localhost)
    $host = "localhost";

    // Nome do banco de dados
    $databasename = "tio_Broker";

    // Nome de usuário do MySQL
    $username = "root";

    // Lista de senhas para tentar — primeiro com "root", depois sem senha
    $senhaTentativas = ["root", ""];

    // Loop para tentar cada senha até conseguir conectar
    foreach ($senhaTentativas as $senha) {

        // Tenta estabelecer a conexão com a senha atual
        // O "@" suprime mensagens de erro caso a tentativa falhe
        $connection = @new mysqli($host, $username, $senha, $databasename);

        // Verifica se a conexão foi bem-sucedida
        if (!$connection->connect_error) {
            // Se conectou com sucesso, retorna a conexão
            return $connection;
        }
    }

    // Se nenhuma senha funcionou, exibe uma mensagem de erro e encerra o script
    die("❌ Não foi possível conectar ao banco de dados com nenhuma das senhas testadas.");
}

// Usa a função para obter a conexão
$connection = conectarBanco();
