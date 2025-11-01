<!--
| ARQUIVO: relatorio_leads_content.php
| View de relatórios com o tema claro aplicado, seguindo o estilo do 'resumo_content.php'.
--><!-- Fundo da página em cinza claro, como o 'resumo_content.php' --><div class="p-6 bg-gray-50 min-h-screen">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">📊 Relatório de Leads</h1>

    <!-- Formulário dentro de um card branco, como o 'resumo_content.php' -->
    <!-- AJUSTE: Mudei o grid para 4 colunas para acomodar os novos filtros -->
    <form method="POST" action="relatorio_leads.php" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-5 rounded-xl shadow-md mb-6">
        <div>
            <!-- Removidas classes 'dark:' --><label class="text-sm font-medium text-gray-700">Data início</label>
            <input type="date" name="dataInicio" class="input-text" value="<?= $_POST['dataInicio'] ?? '' ?>">
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700">Data fim</label>
            <input type="date" name="dataFim" class="input-text" value="<?= $_POST['dataFim'] ?? '' ?>">
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700">Status</label>
            <select name="status" class="input-text">
                <option value="">Todos</option>
                <option value="Novo" <?= ($_POST['status'] ?? '') == 'Novo' ? 'selected' : '' ?>>Novo</option>
                <option value="Contato" <?= ($_POST['status'] ?? '') == 'Contato' ? 'selected' : '' ?>>Em Contato</option>
                <option value="Negociação" <?= ($_POST['status'] ?? '') == 'Negociação' ? 'selected' : '' ?>>Negociação</option>
                <option value="Fechado" <?= ($_POST['status'] ?? '') == 'Fechado' ? 'selected' : '' ?>>Fechado</option>
                <option value="Perdido" <?= ($_POST['status'] ?? '') == 'Perdido' ? 'selected' : '' ?>>Perdido</option>
            </select>
        </div>
        
        <!-- NOVO: Campo de Responsável alterado para <select> -->
        <div>
            <label class="text-sm font-medium text-gray-700">Responsável</label>
            <!-- Supõe que $lista_usuarios (com id_usuario e nome) vem do Controller -->
            <select name="responsavel" class="input-text">
                <option value="">Todos</option>
                <?php if (isset($lista_usuarios) && is_array($lista_usuarios)): ?>
                    <?php foreach ($lista_usuarios as $usuario): ?>
                        <option value="<?= $usuario['id_usuario'] ?>" <?= (isset($_POST['responsavel']) && $_POST['responsavel'] == $usuario['id_usuario']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($usuario['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <!-- NOVO: Campo de Imobiliária alterado para <select> -->
        <div>
            <label class="text-sm font-medium text-gray-700">Imobiliária</label>
            <!-- Supõe que $lista_imobiliarias (com id_imobiliaria e nome) vem do Controller -->
            <select name="imobiliaria" class="input-text">
                <option value="">Todas</option>
                 <?php if (isset($lista_imobiliarias) && is_array($lista_imobiliarias)): ?>
                    <?php foreach ($lista_imobiliarias as $imobiliaria): ?>
                        <option value="<?= $imobiliaria['id_imobiliaria'] ?>" <?= (isset($_POST['imobiliaria']) && $_POST['imobiliaria'] == $imobiliaria['id_imobiliaria']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($imobiliaria['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <!-- NOVO FILTRO: Origem -->
        <div>
            <label class="text-sm font-medium text-gray-700">Origem</label>
            <input type="text" name="origem" placeholder="Ex: Facebook, Google" class="input-text" value="<?= $_POST['origem'] ?? '' ?>">
        </div>

        <!-- NOVO FILTRO: Interesse -->
        <div>
            <label class="text-sm font-medium text-gray-700">Interesse</label>
            <input type="text" name="interesse" placeholder="Ex: Apto 2 quartos" class="input-text" value="<?= $_POST['interesse'] ?? '' ?>">
        </div>

        <!-- AJUSTE: Botão agora ocupa a largura toda do grid (4 colunas) -->
        <div class="col-span-1 md:col-span-4 flex justify-end">
            <button type="submit" class="btn-primary">Gerar Relatório</button>
        </div>
    </form>

    <!-- RF09 Fluxo de Erro: Exibe a mensagem (estilo já é light) --><?php if (isset($_SESSION['mensagem'])): ?>
        <div class="bg-yellow-100 text-yellow-800 p-3 rounded-xl mb-4">
            <?= $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?>
        </div>
    <?php endif; ?>

    <!-- RF09 Fluxo de Sucesso: Exibe a tabela e o gráfico --><?php if (!empty($dados)): ?>
        <!-- Tabela em card branco (Largura total) --><div class="overflow-x-auto bg-white rounded-xl shadow-md p-4">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="py-2 px-3 text-left">Status</th>
                        <th class="py-2 px-3 text-left">Total</th>
                        <th class="py-2 px-3 text-left">Tempo médio (h)</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800">
                    <?php foreach ($dados as $linha): ?>
                        <!-- Linhas da tabela sem classes dark --><tr class="border-t border-gray-200 hover:bg-gray-50 transition">
                            <td class="py-2 px-3"><?= ucfirst($linha['status']) ?></td>
                            <td class="py-2 px-3"><?= $linha['total'] ?></td>
                            <td class="py-2 px-3"><?= number_format($linha['tempo_medio'] ?? 0, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- AJUSTE: Grid para os dois gráficos --><div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Coluna 1: Gráfico de Barras --><div class="bg-white rounded-xl shadow-md p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Leads por Status (Barras)</h3>
                <div class="relative h-64">
                    <canvas id="graficoLeadsBarra"></canvas>
                </div>
            </div>

            <!-- Coluna 2: Gráfico de Pizza --><div class="bg-white rounded-xl shadow-md p-4">
                 <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribuição (Pizza)</h3>
                <div class="relative h-64">
                    <canvas id="graficoLeadsPizza"></canvas>
                </div>
            </div>

        </div>

        <!-- O Chart.js é carregado aqui, com os dados do PHP --><script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Passa os dados do PHP para o JavaScript
            const dadosRelatorio = <?= json_encode($dados) ?>;
            
            const labels = dadosRelatorio.map(d => d.status);
            const totals = dadosRelatorio.map(d => d.total);

            // --- AJUSTE: Mapeamento de Cores Dinâmico ---
            const colorMap = {
                'Novo': 'rgba(59, 130, 246, 0.7)',     // Azul (imagem)
                'Contato': 'rgba(234, 179, 8, 0.7)',   // Amarelo (imagem)
                'Negociação': 'rgba(168, 85, 247, 0.7)', // Roxo (imagem)
                'Fechado': 'rgba(16, 185, 129, 0.7)',  // Verde (imagem)
                'Perdido': 'rgba(239, 68, 68, 0.7)',   // Vermelho (imagem)
                'default': 'rgba(107, 114, 128, 0.7)' // Cinza (padrão)
            };
            
            const borderMap = {
                'Novo': 'rgba(59, 130, 246, 1)',
                'Contato': 'rgba(234, 179, 8, 1)',
                'Negociação': 'rgba(168, 85, 247, 1)',
                'Fechado': 'rgba(16, 185, 129, 1)',
                'Perdido': 'rgba(239, 68, 68, 1)',
                'default': 'rgba(107, 114, 128, 1)'
            };

            // Mapeia os labels (que são os status) para as cores corretas
            const backgroundColors = labels.map(label => colorMap[label] || colorMap['default']);
            const borderColors = labels.map(label => borderMap[label] || borderMap['default']);
            // --- FIM DO AJUSTE ---


            // --- GRÁFICO 1: BARRAS ---
            const ctxBarra = document.getElementById('graficoLeadsBarra');
            if (ctxBarra) {
                new Chart(ctxBarra, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Leads por Status',
                            data: totals,
                            backgroundColor: backgroundColors, // Aplicando cores dinâmicas
                            borderColor: borderColors,       // Aplicando cores dinâmicas
                            borderWidth: 1,
                            barPercentage: 0.5, 
                            categoryPercentage: 0.7 
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true } },
                        plugins: { legend: { display: false } } 
                    }
                });
            }

            // --- GRÁFICO 2: PIZZA (NOVO) ---
            const ctxPizza = document.getElementById('graficoLeadsPizza');
            if (ctxPizza) {
                 new Chart(ctxPizza, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Leads',
                            data: totals,
                            backgroundColor: backgroundColors, // Aplicando cores dinâmicas
                            borderColor: borderColors,       // Aplicando cores dinâmicas
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom', // Legenda na parte inferior
                            }
                        }
                    }
                });
            }
        </script>
    <?php endif; ?>
</div>

