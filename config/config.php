<?php

// Define uma função chamada conectarBanco, responsável por se conectar ao banco de dados
function conectarBanco()
{
    // Nome do host onde o banco está rodando (geralmente localhost)
    $host = "db";

    // Nome do banco de dados a ser acessado
    $databasename = "tio_broker";

    // Nome do usuário do MySQL
    $username = "root";

    // Lista de senhas a serem testadas na conexão: primeiro sem senha, depois com 'root'
    $senhaTentativas = ["", "root"];

    // Percorre cada senha da lista de tentativas
    foreach ($senhaTentativas as $senha) {
        try {
            // Tenta criar uma nova conexão com o banco usando a senha atual
            $conexao = new mysqli($host, $username, $senha, $databasename);

            // Verifica se a conexão foi estabelecida com sucesso (sem erro)
            if (!$conexao->connect_error) {
                // Se conectou corretamente, retorna a conexão para ser usada no sistema
                return $conexao;
            }
        } catch (mysqli_sql_exception $e) {
            // Caso ocorra erro de conexão (como senha inválida), ignora e tenta a próxima senha
            continue;
        }
    }

    // Se nenhuma das senhas funcionou, exibe mensagem de erro e encerra o script
    die("❌ Falha ao conectar ao banco de dados. Verifique as credenciais.");
}

// Executa a função conectarBanco e armazena a conexão na variável $connection
$connection = conectarBanco();
