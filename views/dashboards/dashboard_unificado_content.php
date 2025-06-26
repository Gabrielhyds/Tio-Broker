<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteção: Garante que o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php'); // Ajuste o caminho se necessário
    exit;
}

require_once __DIR__ . '/../../config/config.php';
$mysqli = $connection; // Usa a conexão já estabelecida

// Pega os dados do usuário da sessão para personalização
$permissao = $_SESSION['usuario']['permissao'] ?? '';
$nomeUsuario = $_SESSION['usuario']['nome'] ?? '';
$idUsuarioLogado = (int)($_SESSION['usuario']['id_usuario'] ?? 0);
$idImobiliaria = (int)($_SESSION['usuario']['id_imobiliaria'] ?? 0);

// --- Lógica para buscar dados dos cards dinamicamente ---
$dadosCards = [];

// --- SUPERADMIN ---
if ($permissao === 'SuperAdmin') {
    // Total de imobiliárias ativas
    $result = $mysqli->query("SELECT COUNT(*) AS total FROM imobiliaria WHERE is_deleted = 0");
    $dadosCards['total_imobiliarias'] = $result ? $result->fetch_assoc()['total'] : 0;

    // Total de usuários ativos no sistema
    $result = $mysqli->query("SELECT COUNT(*) AS total FROM usuario WHERE is_deleted = 0");
    $dadosCards['total_usuarios_sistema'] = $result ? $result->fetch_assoc()['total'] : 0;
}
// --- ADMIN ---
elseif ($permissao === 'Admin' && $idImobiliaria > 0) {
    // Total de usuários da imobiliária
    $stmt = $mysqli->prepare("SELECT COUNT(*) AS total FROM usuario WHERE id_imobiliaria = ? AND is_deleted = 0");
    $stmt->bind_param("i", $idImobiliaria);
    $stmt->execute();
    $dadosCards['total_usuarios'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    // Total de clientes da imobiliária
    // --- CORRIGIDO ---: Removida a verificação 'is_deleted' para evitar o erro.
    $stmt = $mysqli->prepare("SELECT COUNT(*) AS total FROM cliente WHERE id_imobiliaria = ?");
    $stmt->bind_param("i", $idImobiliaria);
    $stmt->execute();
    $dadosCards['total_clientes'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();
}
// --- COORDENADOR ---
elseif ($permissao === 'Coordenador' && $idImobiliaria > 0) {
    // Total de corretores na sua imobiliária
    $stmt = $mysqli->prepare("SELECT COUNT(*) AS total FROM usuario WHERE id_imobiliaria = ? AND permissao = 'Corretor' AND is_deleted = 0");
    $stmt->bind_param("i", $idImobiliaria);
    $stmt->execute();
    $dadosCards['total_corretores'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    // Total de clientes da imobiliária
    // --- CORRIGIDO ---: Removida a verificação 'is_deleted' para evitar o erro.
    $stmt = $mysqli->prepare("SELECT COUNT(*) AS total FROM cliente WHERE id_imobiliaria = ?");
    $stmt->bind_param("i", $idImobiliaria);
    $stmt->execute();
    $dadosCards['total_clientes'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();
}
// --- CORRETOR ---
elseif ($permissao === 'Corretor') {
    // Total de clientes do corretor
    // --- CORRIGIDO ---: Removida a verificação 'is_deleted' para evitar o erro.
    $stmt = $mysqli->prepare("SELECT COUNT(*) AS total FROM cliente WHERE id_usuario = ?");
    $stmt->bind_param("i", $idUsuarioLogado);
    $stmt->execute();
    $dadosCards['meus_clientes'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    // Total de tarefas pendentes do corretor
    $stmt = $mysqli->prepare("SELECT COUNT(*) AS total FROM tarefas WHERE id_usuario = ? AND status = 'pendente'");
    $stmt->bind_param("i", $idUsuarioLogado);
    $stmt->execute();
    $dadosCards['tarefas_pendentes'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();
}
?>
<div class="p-6">
    <!-- Cabeçalho -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Bem-vindo, <?= htmlspecialchars(explode(' ', $nomeUsuario)[0]) ?>!</h1>
        <p class="text-gray-500">Aqui está um resumo da sua atividade.</p>
    </div>

    <!-- Grid de Cards Dinâmicos -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card de Perfil de Acesso (para todos) -->
        <div class="bg-white p-5 rounded-lg shadow-sm">
            <p class="text-sm font-medium text-gray-500">Perfil de Acesso</p>
            <p class="text-2xl font-bold text-blue-600 mt-1"><?= htmlspecialchars($permissao) ?></p>
        </div>

        <!-- Cards específicos por permissão -->
        <?php if ($permissao === 'SuperAdmin'): ?>
            <div class="bg-white p-5 rounded-lg shadow-sm">
                <p class="text-sm font-medium text-gray-500">Imobiliárias Ativas</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= $dadosCards['total_imobiliarias'] ?></p>
            </div>
            <div class="bg-white p-5 rounded-lg shadow-sm">
                <p class="text-sm font-medium text-gray-500">Total de Usuários</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= $dadosCards['total_usuarios_sistema'] ?></p>
            </div>
        <?php elseif ($permissao === 'Admin'): ?>
            <div class="bg-white p-5 rounded-lg shadow-sm">
                <p class="text-sm font-medium text-gray-500">Usuários na Equipe</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= $dadosCards['total_usuarios'] ?></p>
            </div>
            <div class="bg-white p-5 rounded-lg shadow-sm">
                <p class="text-sm font-medium text-gray-500">Total de Clientes</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= $dadosCards['total_clientes'] ?></p>
            </div>
        <?php elseif ($permissao === 'Coordenador'): ?>
            <div class="bg-white p-5 rounded-lg shadow-sm">
                <p class="text-sm font-medium text-gray-500">Corretores na Equipe</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= $dadosCards['total_corretores'] ?></p>
            </div>
            <div class="bg-white p-5 rounded-lg shadow-sm">
                <p class="text-sm font-medium text-gray-500">Total de Clientes</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= $dadosCards['total_clientes'] ?></p>
            </div>
        <?php elseif ($permissao === 'Corretor'): ?>
            <div class="bg-white p-5 rounded-lg shadow-sm">
                <p class="text-sm font-medium text-gray-500">Meus Clientes</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= $dadosCards['meus_clientes'] ?></p>
            </div>
            <div class="bg-white p-5 rounded-lg shadow-sm">
                <p class="text-sm font-medium text-gray-500">Tarefas Pendentes</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= $dadosCards['tarefas_pendentes'] ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Seção de Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Funil de Vendas</h3>
            <canvas id="funilVendasChart"></canvas>
        </div>
        <div class="lg:col-span-3 bg-white p-6 rounded-lg shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Clientes por Status</h3>
            <canvas id="clientesStatusChart"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // NOTA: Os dados dos gráficos são estáticos. Para torná-los dinâmicos,
        // você precisaria buscar esses dados do banco via PHP e passá-los para o JavaScript.

        // Gráfico: Funil de Vendas (Doughnut)
        const funilCtx = document.getElementById('funilVendasChart')?.getContext('2d');
        if (funilCtx) {
            new Chart(funilCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Prospects', 'Qualificados', 'Propostas', 'Ganhos'],
                    datasets: [{
                        data: [120, 75, 40, 25],
                        backgroundColor: ['#60a5fa', '#3b82f6', '#2563eb', '#1d4ed8']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Gráfico: Clientes por Status (Barra Horizontal)
        const statusCtx = document.getElementById('clientesStatusChart')?.getContext('2d');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'bar',
                data: {
                    labels: ['Contato Inicial', 'Follow-up', 'Negociação', 'Pós-venda'],
                    datasets: [{
                        label: 'Nº de Clientes',
                        data: [50, 25, 15, 10],
                        backgroundColor: 'rgba(37, 99, 235, 0.6)',
                        borderColor: 'rgba(37, 99, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    });
</script>