<?php
$host = "localhost";
$databasename = "tio_Broker";
$username = "root";

// Array de senhas possíveis (primeiro tenta com senha 'root', depois em branco)
$senhas = ["root", ""];

// Inicializa a variável de conexão como null
$connection = null;
$conectado = false;

// Tenta conectar com cada senha
foreach ($senhas as $senha) {
    $conn = @new mysqli($host, $username, $senha, $databasename);

    if (!$conn->connect_error) {
        $connection = $conn;
        $conectado = true;
        define('DB_PASSWORD_USADA', $senha);
        break; // para o loop ao conectar com sucesso
    }
}

// Verifica se conseguiu conectar
if (!$conectado || !$connection) {
    die("❌ Erro ao conectar no banco de dados: " . $conn->connect_error);
}

// ✅ Conectado com sucesso. Opcional: mostrar a senha usada (só para debug).
// echo "🔐 Conectado com senha: " . (DB_PASSWORD_USADA === "" ? "[vazia]" : DB_PASSWORD_USADA);
