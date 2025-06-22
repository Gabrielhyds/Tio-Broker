<?php
$host = "localhost";
$databasename = "tio_Broker";
$username = "root";

// Tenta conectar sem senha
$senhaTentada = "root";
$connection = @new mysqli($host, $username, $senhaTentada, $databasename);

if ($connection->connect_error) {
    // Tenta com senha padrão
    $senhaTentada = "root";
    $connection = @new mysqli($host, $username, $senhaTentada, $databasename);

    if ($connection->connect_error) {
        // Se falhar novamente, exibe o erro final
        die("Erro de conexão com MySQL: " . $connection->connect_error);
    } else {
        echo "✅ Conectado com senha padrão ('$senhaTentada').";
    }
} else {
    //echo "✅ Conectado sem senha.";
}

// A partir daqui, sua conexão $connection está pronta para uso.
