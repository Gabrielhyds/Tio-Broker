<?php

function conectarBanco()
{
    $host = "localhost";
    $databasename = "tio_Broker";
    $username = "root";

    // Lista de senhas para tentar: primeiro sem senha, depois com 'root'
    $senhaTentativas = ["", "root"];

    foreach ($senhaTentativas as $senha) {
        try {
            // Tenta conectar com a senha atual
            $conexao = new mysqli($host, $username, $senha, $databasename);

            // Verifica se conectou
            if (!$conexao->connect_error) {
                return $conexao; // sucesso
            }
        } catch (mysqli_sql_exception $e) {
            // Ignora a exceção e tenta a próxima senha
            continue;
        }
    }

    // Se chegou aqui, nenhuma tentativa funcionou
    die("❌ Falha ao conectar ao banco de dados. Verifique as credenciais.");
}

// Usa a função para conectar
$connection = conectarBanco();
