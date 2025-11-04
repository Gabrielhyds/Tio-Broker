<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "tio_broker";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$nome = $_POST['nome'];
$email = $_POST['email'];
$contrato_texto = $_POST['contrato_texto'];
$data_assinatura = date("Y-m-d H:i:s");
$ip = $_SERVER['REMOTE_ADDR'];

// A “assinatura simulada” pode ser o nome + data + IP, como uma espécie de hash simples
$assinatura_simulada = md5($nome . $email . $data_assinatura . $ip);

$sql = "INSERT INTO contratos (nome_cliente, email_cliente, contrato_texto, assinatura_simulada, data_assinatura, ip_assinante)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $nome, $email, $contrato_texto, $assinatura_simulada, $data_assinatura, $ip);

if ($stmt->execute()) {
    header("listar_contratos.php");
} else {
    echo "Erro: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
