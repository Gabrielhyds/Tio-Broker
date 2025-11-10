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
$nomeUsuario = $_SESSION['usuario']['nome'] ?? 'Usuário';
$idUsuarioLogado = (int)($_SESSION['usuario']['id_usuario'] ?? 0);
$idImobiliaria = (int)($_SESSION['usuario']['id_imobiliaria'] ?? 0);

// **NOVO**: Buscar o nome da imobiliária do usuário logado
$nomeImobiliaria = '';
if ($permissao !== 'SuperAdmin' && $idImobiliaria > 0) {
    $stmt = $mysqli->prepare("SELECT nome FROM imobiliaria WHERE id_imobiliaria = ?");
    if ($stmt) {
        $stmt->bind_param("i", $idImobiliaria);
        $stmt->execute();
        $resultadoImobiliaria = $stmt->get_result()->fetch_assoc();
        $nomeImobiliaria = $resultadoImobiliaria['nome'] ?? '';
        $stmt->close();
    }
}


// --- Lógica para buscar dados ---
$dadosCards = [];
$tarefasRecentes = [];

// --- Lógica para os CARDS ---
if ($permissao === 'SuperAdmin') {
    $result = $mysqli->query("SELECT COUNT(*) AS total FROM imobiliaria WHERE is_deleted = 0");
    $dadosCards['total_imobiliarias'] = $result->fetch_assoc()['total'] ?? 0;
    $result = $mysqli->query("SELECT COUNT(*) AS total FROM usuario WHERE is_deleted = 0");
    $dadosCards['total_usuarios_sistema'] = $result->fetch_assoc()['total'] ?? 0;
    // SuperAdmin vê todos os imóveis
    $result = $mysqli->query("SELECT COUNT(*) AS total FROM imovel");
    $dadosCards['total_imoveis'] = $result->fetch_assoc()['total'] ?? 0;
} else {
    // Total de Clientes (usado por Admin, Coordenador)
    if ($permissao === 'Admin' || $permissao === 'Coordenador') {
        $stmt = $mysqli->prepare("SELECT COUNT(*) AS total FROM cliente WHERE id_imobiliaria = ?");
        $stmt->bind_param("i", $idImobiliaria);
        $stmt->execute();
        $dadosCards['total_clientes'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
        $stmt->close();
    }
    // Total de Usuários (Admin)
    if ($permissao === 'Admin') {
        $stmt = $mysqli->prepare("SELECT COUNT(*) AS total FROM usuario WHERE id_imobiliaria = ? AND is_deleted = 0");
        $stmt->bind_param("i", $idImobiliaria);
        $stmt->execute();
        $dadosCards['total_usuarios'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
        $stmt->close();
    }
    // Total de Corretores (Coordenador)
    if ($permissao === 'Coordenador') {
        $stmt = $mysqli->prepare("SELECT COUNT(*) AS total FROM usuario WHERE id_imobiliaria = ? AND permissao = 'Corretor' AND is_deleted = 0");
        $stmt->bind_param("i", $idImobiliaria);
        $stmt->execute();
        $dadosCards['total_corretores'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
        $stmt->close();
    }
    // Meus Clientes (Corretor)
    if ($permissao === 'Corretor') {
        $stmt = $mysqli->prepare("SELECT COUNT(*) AS total FROM cliente WHERE id_usuario = ?");
        $stmt->bind_param("i", $idUsuarioLogado);
        $stmt->execute();
        $dadosCards['meus_clientes'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
        $stmt->close();
    }

    // Total de imóveis cadastrados por imobiliária.
    if ($idImobiliaria > 0) {
        $stmt = $mysqli->prepare("SELECT COUNT(*) AS total FROM imovel WHERE id_imobiliaria = ?");
        $stmt->bind_param("i", $idImobiliaria);
        $stmt->execute();
        $dadosCards['total_imoveis'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
        $stmt->close();
    } else {
        $dadosCards['total_imoveis'] = 0;
    }
}

// --- Lógica para os GRÁFICOS ---
$dadosGraficoFunil = [
    'labels' => ['Prospects', 'Qualificados', 'Propostas', 'Ganhos'],
    'data' => [120, 75, 40, 25]
];
$dadosGraficoStatus = [
    'labels' => ['Contato Inicial', 'Follow-up', 'Negociação', 'Pós-venda'],
    'data' => [50, 25, 15, 10]
];


// --- Lógica para TAREFAS ---
$stmt = $mysqli->prepare("SELECT id_tarefa, descricao, prazo, status FROM tarefas WHERE id_usuario = ? AND status != 'concluida' ORDER BY prazo ASC");
$stmt->bind_param("i", $idUsuarioLogado);
$stmt->execute();
$tarefasRecentes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$hoje = date('Y-m-d'); // Para comparar e saber se a tarefa está vencida

?>
<div class="p-4 sm:p-6 bg-gray-50 min-h-screen">
    <!-- Cabeçalho -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Bem-vindo, <?= htmlspecialchars(explode(' ', $nomeUsuario)[0]) ?>!</h1>
        <p class="text-gray-500">Aqui está um resumo da sua atividade hoje.</p>

        <!-- **NOVO**: Exibe o nome da imobiliária -->
        <?php if (!empty($nomeImobiliaria)): ?>
            <div class="mt-3 inline-flex items-center gap-2 bg-cyan-100 text-cyan-800 text-sm font-medium px-4 py-2 rounded-full shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                <span><?= htmlspecialchars($nomeImobiliaria) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Grid de Cards Dinâmicos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card de Perfil de Acesso -->
        <div class="bg-white p-5 rounded-xl shadow-md transition-transform hover:scale-105">
            <div class="flex items-center gap-4">
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Perfil de Acesso</p>
                    <p class="text-xl font-bold text-blue-600"><?= htmlspecialchars($permissao) ?></p>
                </div>
            </div>
        </div>

        <!-- Card de Imóveis Cadastrados -->
        <div class="bg-white p-5 rounded-xl shadow-md transition-transform hover:scale-105">
            <p class="text-sm font-medium text-gray-500">Imóveis Cadastrados</p>
            <p class="text-3xl font-bold text-gray-900 mt-1"><?= $dadosCards['total_imoveis'] ?? 0 ?></p>
        </div>

        <!-- Cards específicos por permissão -->
        <?php if ($permissao === 'SuperAdmin'): ?>
            <div class="bg-white p-5 rounded-xl shadow-md transition-transform hover:scale-105">
                <p class="text-sm font-medium text-gray-500">Imobiliárias Ativas</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= $dadosCards['total_imobiliarias'] ?></p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-md transition-transform hover:scale-105">
                <p class="text-sm font-medium text-gray-500">Total de Usuários</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= $dadosCards['total_usuarios_sistema'] ?></p>
            </div>
        <?php elseif ($permissao === 'Admin'): ?>
            <div class="bg-white p-5 rounded-xl shadow-md transition-transform hover:scale-105">
                <p class="text-sm font-medium text-gray-500">Usuários na Equipe</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= $dadosCards['total_usuarios'] ?></p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-md transition-transform hover:scale-105">
                <p class="text-sm font-medium text-gray-500">Total de Clientes</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= $dadosCards['total_clientes'] ?></p>
            </div>
        <?php elseif ($permissao === 'Coordenador'): ?>
            <div class="bg-white p-5 rounded-xl shadow-md transition-transform hover:scale-105">
                <p class="text-sm font-medium text-gray-500">Corretores na Equipe</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= $dadosCards['total_corretores'] ?></p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-md transition-transform hover:scale-105">
                <p class="text-sm font-medium text-gray-500">Total de Clientes</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= $dadosCards['total_clientes'] ?></p>
            </div>
        <?php elseif ($permissao === 'Corretor'): ?>
            <div class="bg-white p-5 rounded-xl shadow-md transition-transform hover:scale-105">
                <p class="text-sm font-medium text-gray-500">Meus Clientes</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= $dadosCards['meus_clientes'] ?? 0 ?></p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-md transition-transform hover:scale-105">
                <p class="text-sm font-medium text-gray-500">Tarefas Pendentes</p>
                <p class="text-3xl font-bold text-red-500 mt-1"><?= count($tarefasRecentes) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Seção de Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-md">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Funil de Vendas</h3>
            <div class="h-64">
                <canvas id="funilVendasChart"></canvas>
            </div>
        </div>
        <div class="lg:col-span-3 bg-white p-6 rounded-xl shadow-md">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Clientes por Status</h3>
            <div class="h-64">
                <canvas id="clientesStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Seção de Tarefas agora ocupa a largura total. -->
    <div class="grid grid-cols-1 gap-6">
        <!-- Tarefas Pendentes e Vencidas -->
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Suas Tarefas (Pendentes e Vencidas)</h3>
            <div class="space-y-4">
                <?php if (!empty($tarefasRecentes)): ?>
                    <?php foreach ($tarefasRecentes as $tarefa): ?>
                        <?php
                        $vencida = !empty($tarefa['prazo']) && $tarefa['prazo'] < $hoje;
                        $corTexto = $vencida ? 'text-red-600' : 'text-yellow-600';
                        $corFundo = $vencida ? 'bg-red-100' : 'bg-yellow-100';
                        $textoPrazo = $vencida ? 'Vencida em: ' : 'Vence em: ';
                        ?>
                        <a href="/tarefas/edit/<?= $tarefa['id_tarefa'] ?>" class="block hover:bg-gray-50 p-3 rounded-lg">
                            <div class="flex items-center justify-between">
                                <p class="font-medium text-gray-700"><?= htmlspecialchars($tarefa['descricao']) ?></p>
                                <?php if (!empty($tarefa['prazo'])): ?>
                                    <span class="text-sm font-semibold <?= $corTexto ?> <?= $corFundo ?> px-2 py-1 rounded-full">
                                        <?= $textoPrazo . date("d/m/Y", strtotime($tarefa['prazo'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-sm text-gray-400 bg-gray-100 px-2 py-1 rounded-full">
                                        Sem prazo
                                    </span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma tarefa pendente</h3>
                        <p class="mt-1 text-sm text-gray-500">Você está em dia!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Passando os dados do PHP para o JavaScript de forma segura
        const dadosFunil = <?= json_encode($dadosGraficoFunil); ?>;
        const dadosStatus = <?= json_encode($dadosGraficoStatus); ?>;

        // Cores para os gráficos
        const coresFunil = ['#60a5fa', '#3b82f6', '#2563eb', '#1d4ed8', '#1e40af'];
        const corBarras = 'rgba(59, 130, 246, 0.7)';
        const corBordas = 'rgba(59, 130, 246, 1)';

        // Gráfico: Funil de Vendas (Doughnut)
        const funilCtx = document.getElementById('funilVendasChart')?.getContext('2d');
        if (funilCtx && dadosFunil.labels.length > 0) {
            new Chart(funilCtx, {
                type: 'doughnut',
                data: {
                    labels: dadosFunil.labels,
                    datasets: [{
                        data: dadosFunil.data,
                        backgroundColor: coresFunil,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
        if (statusCtx && dadosStatus.labels.length > 0) {
            new Chart(statusCtx, {
                type: 'bar',
                data: {
                    labels: dadosStatus.labels,
                    datasets: [{
                        label: 'Nº de Clientes',
                        data: dadosStatus.data,
                        backgroundColor: corBarras,
                        borderColor: corBordas,
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
                    maintainAspectRatio: false,
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