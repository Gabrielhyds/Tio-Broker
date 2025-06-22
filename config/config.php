<?php
$host = "localhost";
$databasename = "tio_Broker";
$username = "root";

// Lista de senhas a testar, em ordem de prioridade
$senhas = ["root", ""];

// Tenta se conectar com cada senha até funcionar
foreach ($senhas as $senhaTentada) {
    $connection = @new mysqli($host, $username, $senhaTentada, $databasename);

    if (!$connection->connect_error) {
        // Conexão bem-sucedida
        // Define a senha usada para futuras referências, se quiser
        define('DB_PASSWORD_USADA', $senhaTentada);
        break;
    }
}

// Se ainda houver erro, exibe e encerra
if ($connection->connect_error) {
    die("❌ Erro ao conectar no banco de dados: " . $connection->connect_error);
}

// ✅ Se chegou aqui, está conectado com sucesso!