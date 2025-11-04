<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "tio_broker";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$sql = "SELECT * FROM contratos ORDER BY data_assinatura DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Contratos Assinados (Simulação)</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <div class="max-w-5xl mx-auto mt-12 bg-white shadow-lg rounded-xl p-8">
        <h1 class="text-2xl font-semibold text-gray-700 mb-6 text-center">
            📄 Contratos Assinados (Simulação)
        </h1>

        <?php if ($result->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-600 text-left">
                            <th class="px-4 py-2 border-b">#</th>
                            <th class="px-4 py-2 border-b">Nome</th>
                            <th class="px-4 py-2 border-b">E-mail</th>
                            <th class="px-4 py-2 border-b">Data</th>
                            <th class="px-4 py-2 border-b">IP</th>
                            <th class="px-4 py-2 border-b">Assinatura</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border-b"><?= htmlspecialchars($row['id']) ?></td>
                                <td class="px-4 py-2 border-b"><?= htmlspecialchars($row['nome_cliente']) ?></td>
                                <td class="px-4 py-2 border-b"><?= htmlspecialchars($row['email_cliente']) ?></td>
                                <td class="px-4 py-2 border-b">
                                    <?= date('d/m/Y H:i', strtotime($row['data_assinatura'])) ?>
                                </td>
                                <td class="px-4 py-2 border-b"><?= htmlspecialchars($row['ip_assinante']) ?></td>
                                <td class="px-4 py-2 border-b text-green-600 font-medium">
                                    <?= htmlspecialchars($row['assinatura_simulada']) ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500 mt-6">Nenhum contrato encontrado.</p>
        <?php endif; ?>

        <div class="text-center mt-8">
            <a href="<?= BASE_URL ?>views/contratos/assinar_teste.php" 
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg transition">
               Assinar Novo Contrato
            </a>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>