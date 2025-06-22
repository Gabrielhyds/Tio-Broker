<?php
$host = "localhost";
$databasename = "tio_Broker";
$username = "root";
$senhas = ["root", ""];

$connection = null;

foreach ($senhas as $senha) {
    $conn = @new mysqli($host, $username, $senha, $databasename);
    if (!$conn->connect_error) {
        $connection = $conn;
        define('DB_PASSWORD_USADA', $senha);
        break;
    }
}

if (!$connection) {
    die("❌ Erro ao conectar no banco de dados: " . $conn->connect_error);
}

// Expondo como global (opcional, mas útil)
$GLOBALS['connection'] = $connection;
