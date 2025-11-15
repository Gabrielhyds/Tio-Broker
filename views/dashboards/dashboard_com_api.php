<?php
// =========================================================================
// ARQUIVO "MORTALHA" DO PHP (opcional, mas bom para segurança)
// Este arquivo PHP agora serve apenas para carregar o HTML.
// A proteção de sessão ainda acontece no index.php que carrega este
// e, mais importante, na API.
// =========================================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php'); // Proteção de acesso ao arquivo
    exit;
}
?>

<!-- 
=========================================================================
ETAPA 1: HTML (Sem PHP 'echo' em lugar nenhum)
=========================================================================
-->
<div id="app-dashboard" class="p-4 sm:p-6 bg-gray-50 min-h-screen">

    <!-- MUDANÇA: Adicionamos um estado de "Carregando" -->
    <div v-if="carregando" class="text-center py-20">
        <svg class="mx-auto h-12 w-12 text-blue-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="mt-4 text-lg font-medium text-gray-600">Carregando dados...</p>
    </div>

    <!-- MUDANÇA: O dashboard inteiro só aparece quando não está carregando -->
    <div v-else>
        <!-- Cabeçalho -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Bem-vindo, {{ primeiroNome }}!</h1>
            <p class="text-gray-500">Aqui está um resumo da sua atividade hoje.</p>
            <div v-if="nomeImobiliaria" class="mt-3 inline-flex items-center gap-2 bg-cyan-100 text-cyan-800 text-sm font-medium px-4 py-2 rounded-full shadow-sm">
                 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-2a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd" />
                </svg>
                <span>{{ nomeImobiliaria }}</span>
            </div>
        </div>

        <!-- Os componentes são chamados exatamente da mesma forma! -->
        <!-- Eles não sabem (e não se importam) de onde os dados vieram. -->
        <grid-cards :cards="dadosCards" :permissao="permissao" :total-tarefas="tarefasRecentes.length"></grid-cards>

        <!-- Seção de Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Funil de Vendas</h3>
                <div class="h-64">
                    <canvas id="funilVendasChart"></canvas>
                </div>
            </div>
            <div class="lg:col-span-3 bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Leads por Status</h3>
                <div class="h-64">
                    <canvas id="clientesStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Componente de Tarefas -->
        <lista-tarefas :tarefas="tarefasRecentes" :hoje="hoje"></lista-tarefas>
    </div>
</div>

<!-- ========================================================================= -->
<!-- ETAPA 2: JavaScript (Agora busca dados da API)                     -->
<!-- ========================================================================= -->

<!-- MUDANÇA: Não há mais script "dadosIniciais" injetado pelo PHP -->
    
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const { createApp } = Vue;

    // Variáveis globais para os gráficos, para que possamos destruí-las
    let funilVendasChartInstance = null;
    let clientesStatusChartInstance = null;

    const app = createApp({
        // MUDANÇA: O 'data' agora contém os estados iniciais "vazios"
        // e um novo estado 'carregando'.
        data() {
            return {
                carregando: true,
                nomeUsuario: '',
                nomeImobiliaria: '',
                permissao: '',
                dadosCards: {},
                tarefasRecentes: [],
                dadosGraficoFunil: { labels: [], data: [] }, // Inicia vazio
                dadosGraficoStatus: { labels: [], data: [] }, // Inicia vazio
                hoje: new Date().toISOString().split('T')[0] // Pega 'hoje' do JS
            }
        },
        computed: {
            primeiroNome() {
                if (!this.nomeUsuario) return '';
                return this.nomeUsuario.split(' ')[0];
            }
        },
        methods: {
            // MUDANÇA: Esta função agora é chamada DEPOIS que os dados chegam
            // E O CÓDIGO DO CHART.JS FOI ADICIONADO AQUI
            renderizarGraficos() {
                console.log("Renderizando gráficos com dados da API...", this.dadosGraficoFunil);

                // Destrói gráficos antigos se existirem (para evitar bugs em recarregamentos)
                if (funilVendasChartInstance) {
                    funilVendasChartInstance.destroy();
                }
                if (clientesStatusChartInstance) {
                    clientesStatusChartInstance.destroy();
                }

                // Gráfico 1: Funil de Vendas (Doughnut)
                const ctxFunil = document.getElementById('funilVendasChart');
                if (ctxFunil) {
                    funilVendasChartInstance = new Chart(ctxFunil, {
                        type: 'doughnut',
                        data: {
                            labels: this.dadosGraficoFunil.labels,
                            datasets: [{
                                label: 'Leads',
                                data: this.dadosGraficoFunil.data,
                                backgroundColor: [
                                    '#3b82f6', // blue-500
                                    '#22d3ee', // cyan-400
                                    '#f97316', // orange-500
                                    '#22c55e', // green-500
                                    '#ef4444'  // red-500
                                ],
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                }
                            }
                        }
                    });
                }

                // Gráfico 2: Status (Bar)
                const ctxStatus = document.getElementById('clientesStatusChart');
                if (ctxStatus) {
                    clientesStatusChartInstance = new Chart(ctxStatus, {
                        type: 'bar',
                        data: {
                            labels: this.dadosGraficoStatus.labels,
                            datasets: [{
                                label: 'Total de Leads',
                                data: this.dadosGraficoStatus.data,
                                backgroundColor: '#14b8a6', // teal-500
                                borderColor: '#0f766e', // teal-700
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: 'y', // Gráfico de barras horizontal
                            plugins: {
                                legend: {
                                    display: false // Esconde a legenda para um look mais limpo
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            },

            // MUDANÇA: Nova função assíncrona para buscar dados da API
            async buscarDados() {
                try {
                    // O Vue "puxa" os dados da API que criamos
                    const response = await fetch('../../api/dashboard_data.php');

                    if (!response.ok) {
                        // Se a API retornar 401 (não logado) ou 500 (erro)
                        if (response.status === 401) {
                            // Redireciona para o login
                            window.location.href = '../auth/login.php';
                        }
                        throw new Error(`Erro na API: ${response.statusText}`);
                    }
                    
                    const dados = await response.json();

                    // Preenche o 'data' do Vue com os dados da API
                    this.nomeUsuario = dados.nomeUsuario;
                    this.nomeImobiliaria = dados.nomeImobiliaria;
                    this.permissao = dados.permissao;
                    this.dadosCards = dados.dadosCards;
                    this.tarefasRecentes = dados.tarefasRecentes;
                    this.dadosGraficoFunil = dados.dadosGraficoFunil;
                    this.dadosGraficoStatus = dados.dadosGraficoStatus;
                    this.hoje = dados.hoje;

                } catch (error) {
                    console.error("Falha ao buscar dados do dashboard:", error);
                    // (Opcional) Mostrar uma mensagem de erro para o usuário
                } finally {
                    // MUDANÇA: Para de carregar, independentemente de sucesso ou falha
                    this.carregando = false;

                    // MUDANÇA: Renderiza os gráficos SÓ DEPOIS que o DOM foi
                    // atualizado com 'carregando = false'
                    this.$nextTick(() => {
                        this.renderizarGraficos();
                    });
                }
            }
        },
        // MUDANÇA: 'mounted' agora é super simples: só chama a função de busca
        mounted() {
            this.buscarDados();
        }
    });

    // =====================================================================
    // DEFINIÇÃO DOS COMPONENTES (Exatamente como antes, mas com template)
    // =====================================================================
    // Os componentes não mudam NADA. Eles são "agnósticos"
    // sobre como o pai (a 'app') obteve os dados.
    
    app.component('grid-cards', {
        props: ['cards', 'permissao', 'totalTarefas'],
        // TEMPLATE COMPLETO ADICIONADO
        template: `
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <!-- Card de Perfil (Fixo) -->
                <div class="bg-white p-5 rounded-xl shadow-md flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-blue-600">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-5.5-2.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0ZM10 10.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Perfil de Acesso</p>
                        <p class="text-xl font-bold text-gray-800">{{ permissao }}</p>
                    </div>
                </div>

                <!-- Card de Tarefas (Fixo) -->
                <div class="bg-white p-5 rounded-xl shadow-md flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-yellow-600">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5H10.75V5Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Tarefas Pendentes</p>
                        <p class="text-xl font-bold text-gray-800">{{ totalTarefas }}</p>
                    </div>
                </div>

                <!-- CARDS DINÂMICOS DA API -->
                <!-- SuperAdmin -->
                <div v-if="permissao === 'SuperAdmin'" class="bg-white p-5 rounded-xl shadow-md flex items-center space-x-4">
                    <div class="flex-shrink-0"><div class="bg-indigo-100 p-3 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-indigo-600"><path d="M11.983 1.907a.75.75 0 0 0-1.966 0l-3.25 1.625a.75.75 0 0 0-.517.696V8.75a.75.75 0 0 0 .75.75h.03l.02-.001.02.001h.029l.015-.001.015.001H8.25a.75.75 0 0 0 .75-.75V5.13l2.5-1.25v3.62a.75.75 0 0 0 .75.75h.03l.02-.001.02.001h.029l.015-.001.015.001H12a.75.75 0 0 0 .75-.75V4.228a.75.75 0 0 0-.517-.696l-3.25-1.625Z" /><path d="m5.212 7.378.018-.009.018.009L7 8.216v2.534a.75.75 0 0 1-.75.75h-.03l-.02.001-.02-.001H6.151l-.015.001-.015-.001H6a.75.75 0 0 1-.75-.75V8.216l1.762-.838Z" /><path d="m12.988 7.378.018-.009.018.009L14.788 8.216v2.534a.75.75 0 0 1-.75.75h-.03l-.02.001-.02-.001h-.029l-.015.001-.015-.001H13.8a.75.75 0 0 1-.75-.75V8.216l1.762-.838Z" /><path d="M5.212 11.628.018-.01.018.01 1.762 12.46v2.534a.75.75 0 0 0 .75.75h.03l.02-.001.02.001h.029l.015-.001.015.001H3a.75.75 0 0 0 .75-.75V12.46l1.762-.838Z" /><path d="m12.988 11.628.018-.01.018.01L14.788 12.46v2.534a.75.75 0 0 0 .75.75h.03l.02-.001.02.001h.029l.015-.001.015.001H16.8a.75.75 0 0 0 .75-.75V12.46l1.762-.838Z" /></svg></div></div>
                    <div><p class="text-sm font-medium text-gray-500">Total Imobiliárias</p><p class="text-xl font-bold text-gray-800">{{ cards.total_imobiliarias }}</p></div>
                </div>
                <div v-if="permissao === 'SuperAdmin'" class="bg-white p-5 rounded-xl shadow-md flex items-center space-x-4">
                    <div class="flex-shrink-0"><div class="bg-green-100 p-3 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-green-600"><path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.25 1.25 0 0 0 2.404-1.215 5.5 5.5 0 0 1 1.03-1.63 5.5 5.5 0 0 1 1.63-1.03 1.25 1.25 0 0 0-1.215-2.404 8.001 8.001 0 0 0-4.848 12.28c.433.64 1.2.985 1.996.985H10v-2.5a3.5 3.5 0 0 0-3.5-3.5H3.465ZM10.75 11.25a.75.75 0 0 0-1.5 0v2.5h-2.5a.75.75 0 0 0 0 1.5h2.5v2.5a.75.75 0 0 0 1.5 0v-2.5h2.5a.75.75 0 0 0 0-1.5h-2.5v-2.5Z" /><path d="M14.5 10.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM10 12.5a5.5 5.5 0 0 1 5.5 5.5v2.5h-2.136a8.002 8.002 0 0 0-12.28-4.848 1.25 1.25 0 0 0 1.215 2.404 5.5 5.5 0 0 1 1.63 1.03 5.5 5.5 0 0 1 1.03 1.63 1.25 1.25 0 0 0 2.404 1.215 8.001 8.001 0 0 0 2.671-.985V18a3.5 3.5 0 0 0-3.5-3.5H10v-2Z" /></svg></div></div>
                    <div><p class="text-sm font-medium text-gray-500">Total Usuários</tota></p><p class="text-xl font-bold text-gray-800">{{ cards.total_usuarios_sistema }}</p></div>
                </div>

                <!-- Admin / Coordenador -->
                <div v-if="permissao === 'Admin' || permissao === 'Coordenador'" class="bg-white p-5 rounded-xl shadow-md flex items-center space-x-4">
                    <div class="flex-shrink-0"><div class="bg-teal-100 p-3 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-teal-600"><path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h9.5A2.25 2.25 0 0 1 17 4.25v8.5A2.25 2.25 0 0 1 14.75 15h-3.191l-3.216 3.216a.75.75 0 0 1-1.06 0L4.06 15H3a2.25 2.25 0 0 1-2.25-2.25v-8.5Z" clip-rule="evenodd" /></svg></div></div>
                    <div><p class="text-sm font-medium text-gray-500">Total de Leads</p><p class="text-xl font-bold text-gray-800">{{ cards.total_leads }}</p></div>
                </div>
                <div v-if="permissao === 'Admin' || permissao === 'Coordenador'" class="bg-white p-5 rounded-xl shadow-md flex items-center space-x-4">
                    <div class="flex-shrink-0"><div class="bg-purple-100 p-3 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-purple-600"><path d="M13 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM18 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM13 15a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM13 13.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM4.5 10.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM16.5 10.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM8.5 13.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" /></svg></div></div>
                    <div><p class="text-sm font-medium text-gray-500">Total Clientes</p><p class="text-xl font-bold text-gray-800">{{ cards.total_clientes }}</p></div>
                </div>

                <!-- Corretor -->
                <div v-if="permissao === 'Corretor'" class="bg-white p-5 rounded-xl shadow-md flex items-center space-x-4">
                    <div class="flex-shrink-0"><div class="bg-teal-100 p-3 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-teal-600"><path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h9.5A2.25 2.25 0 0 1 17 4.25v8.5A2.25 2.25 0 0 1 14.75 15h-3.191l-3.216 3.216a.75.75 0 0 1-1.06 0L4.06 15H3a2.25 2.25 0 0 1-2.25-2.25v-8.5Z" clip-rule="evenodd" /></svg></div></div>
                    <div><p class="text-sm font-medium text-gray-500">Meus Leads</p><p class="text-xl font-bold text-gray-800">{{ cards.meus_leads }}</p></div>
                </div>
                <div v-if="permissao === 'Corretor'" class="bg-white p-5 rounded-xl shadow-md flex items-center space-x-4">
                    <div class="flex-shrink-0"><div class="bg-purple-100 p-3 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-purple-600"><path d="M13 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM18 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM13 15a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM13 13.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM4.5 10.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM16.5 10.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM8.5 13.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" /></svg></div></div>
                    <div><p class="text-sm font-medium text-gray-500">Meus Clientes</p><p class="text-xl font-bold text-gray-800">{{ cards.meus_clientes }}</p></div>
                </div>
            </div>
        `
    });

    app.component('lista-tarefas', {
        props: ['tarefas', 'hoje'],
        methods: {
            isTarefaVencida(tarefa) {
                // Compara apenas a data, ignorando a hora
                return tarefa.prazo < this.hoje && tarefa.status !== 'concluida';
            },
            formatarPrazo(tarefa) {
                if (!tarefa.prazo) return 'Sem prazo';
                const data = new Date(tarefa.prazo + 'T00:00:00'); // Corrige fuso horário
                if (tarefa.prazo === this.hoje) {
                    return 'Hoje';
                }
                const amanha = new Date(new Date(this.hoje).setDate(new Date(this.hoje).getDate() + 1)).toISOString().split('T')[0];
                if (tarefa.prazo === amanha) {
                    return 'Amanhã';
                }
                if (this.isTarefaVencida(tarefa)) {
                    return `Venceu em ${data.toLocaleDateString('pt-BR')}`;
                }
                return data.toLocaleDateString('pt-BR');
            },
            getPrazoClasses(tarefa) {
                if (this.isTarefaVencida(tarefa)) {
                    return 'text-red-600 bg-red-100'; // Vencida
                }
                if (tarefa.prazo === this.hoje) {
                    return 'text-yellow-600 bg-yellow-100'; // Hoje
                }
                return 'text-gray-500 bg-gray-100'; // Normal
            }
        },
        // TEMPLATE COMPLETO ADICIONADO
        template: `
            <div class="grid grid-cols-1 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Suas Tarefas Pendentes</h3>
                    
                    <div v-if="!tarefas || tarefas.length === 0" class="text-center py-10">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-10 h-10 text-green-500 mx-auto">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                        </svg>
                        <p class="mt-4 text-gray-500">Você está em dia! Nenhuma tarefa pendente.</p>
                    </div>

                    <ul v-else class="space-y-4">
                        <li v-for="tarefa in tarefas" :key="tarefa.id_tarefa" class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center space-x-3 mb-2 sm:mb-0">
                                <span class="flex-shrink-0 w-3 h-3 rounded-full" :class="isTarefaVencida(tarefa) ? 'bg-red-500' : 'bg-blue-500'"></span>
                                <p class="text-gray-700 font-medium">{{ tarefa.descricao }}</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span :class="getPrazoClasses(tarefa)" class="text-sm font-semibold px-2.5 py-0.5 rounded-full">
                                    {{ formatarPrazo(tarefa) }}
                                </span>
                                <a :href="'../tarefas/index.php?id=' + tarefa.id_tarefa" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                    Ver
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        `
    });

    // Finalmente, monta o app
    app.mount('#app-dashboard');
</script>