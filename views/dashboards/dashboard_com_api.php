<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php'); // Proteção de acesso ao arquivo
    exit;
}
?>
<!-- 
  CORREÇÃO DE PADDING: 
  Adicionado 'p-4 sm:p-6' de volta.
  O 'pt-16' do template_base vai cuidar do espaço do header,
  e este 'p-6' vai dar o respiro interno da página.
-->
<div id="app-dashboard" class="p-4 sm:p-6 bg-gray-50 min-h-screen dark:bg-gray-900">

    <div v-if="carregando" class="text-center py-20">
        <svg class="mx-auto h-12 w-12 text-blue-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="mt-4 text-lg font-medium text-gray-600 dark:text-gray-400">Carregando dados...</p>
    </div>

    <div v-else>
        <!-- Cabeçalho -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">Bem-vindo, {{ primeiroNome }}!</h1>
            <p class="text-gray-500 dark:text-gray-400">Aqui está um resumo da sua atividade hoje.</p>
            <div v-if="nomeImobiliaria" class="mt-3 inline-flex items-center gap-2 bg-cyan-100 text-cyan-800 text-sm font-medium px-4 py-2 rounded-full shadow-sm dark:bg-cyan-900 dark:text-cyan-300">
                 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-2a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd" />
                </svg>
                <span>{{ nomeImobiliaria }}</span>
            </div>
        </div>

        <grid-cards :cards="dadosCards" :permissao="permissao" :total-tarefas="tarefasRecentes.length"></grid-cards>

        <!-- Seção de Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-md dark:bg-gray-800 dark:border dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 dark:text-gray-200">Funil de Vendas</h3>
                <div class="h-64">
                    <canvas id="funilVendasChart"></canvas>
                </div>
            </div>
            <div class="lg:col-span-3 bg-white p-6 rounded-xl shadow-md dark:bg-gray-800 dark:border dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 dark:text-gray-200">Leads por Status</h3>
                <div class="h-64">
                    <canvas id="clientesStatusChart"></canvas>
                </div>
            </div>
        </div>

        <lista-tarefas :tarefas="tarefasRecentes" :hoje="hoje"></lista-tarefas>
    </div>
</div>
    
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const { createApp } = Vue;

    let funilVendasChartInstance = null;
    let clientesStatusChartInstance = null;

    const app = createApp({
        data() {
            return {
                carregando: true,
                nomeUsuario: '',
                nomeImobiliaria: '',
                permissao: '',
                dadosCards: {},
                tarefasRecentes: [],
                dadosGraficoFunil: { labels: [], data: [] },
                dadosGraficoStatus: { labels: [], data: [] },
                hoje: new Date().toISOString().split('T')[0]
            }
        },
        computed: {
            primeiroNome() {
                if (!this.nomeUsuario) return '';
                return this.nomeUsuario.split(' ')[0];
            }
        },
        methods: {
            // MUDANÇA: Gráficos agora reagem ao Dark Mode
            renderizarGraficos() {
                const isDarkMode = document.documentElement.classList.contains('dark');
                const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
                const labelColor = isDarkMode ? '#e5e7eb' : '#374151'; // text-gray-200 vs text-gray-700
                Chart.defaults.color = labelColor; // Cor global da fonte do Chart.js

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
                                hoverOffset: 4,
                                borderColor: isDarkMode ? '#1f2937' : '#ffffff' // Cor da borda (bg-gray-800 ou bg-white)
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { color: labelColor } // Cor da legenda
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
                            indexAxis: 'y',
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    grid: { color: gridColor }, // Cor da grade X
                                    ticks: { color: labelColor } // Cor dos rótulos X
                                },
                                y: {
                                    grid: { color: gridColor }, // Cor da grade Y
                                    ticks: { color: labelColor } // Cor dos rótulos Y
                                }
                            }
                        }
                    });
                }
            },

            async buscarDados() {
                try {
                    const response = await fetch('../../api/dashboard_data.php');
                    if (!response.ok) {
                        if (response.status === 401) {
                            window.location.href = '../auth/login.php';
                        }
                        throw new Error(`Erro na API: ${response.statusText}`);
                    }
                    const dados = await response.json();
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
                } finally {
                    this.carregando = false;
                    this.$nextTick(() => {
                        this.renderizarGraficos();
                    });
                }
            }
        },
        mounted() {
            this.buscarDados();
            
            // NOVO: Ouve o evento de troca de tema para redesenhar os gráficos
            document.getElementById('theme-toggle-btn')?.addEventListener('click', () => {
                // Espera a UI atualizar ANTES de redesenhar
                this.$nextTick(() => {
                    this.renderizarGraficos();
                });
            });
        }
    });

    // =====================================================================
    // DEFINIÇÃO DOS COMPONENTES (COM DARK MODE)
    // =====================================================================
    
    app.component('grid-cards', {
        props: ['cards', 'permissao', 'totalTarefas'],
        template: `
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <div class="bg-white p-5 rounded-xl shadow-md flex items-center space-x-4 dark:bg-gray-800 dark:border dark:border-gray-700">
                    <div class="flex-shrink-0">
                        <div class="bg-blue-100 p-3 rounded-full dark:bg-blue-900/50">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-blue-600 dark:text-blue-300">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-5.5-2.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0ZM10 10.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Perfil de Acesso</p>
                        <p class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ permissao }}</p>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl shadow-md flex items-center space-x-4 dark:bg-gray-800 dark:border dark:border-gray-700">
                    <div class="flex-shrink-0">
                        <div class="bg-yellow-100 p-3 rounded-full dark:bg-yellow-900/50">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-yellow-600 dark:text-yellow-300">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5H10.75V5Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tarefas Pendentes</p>
                        <p class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ totalTarefas }}</p>
                    </div>
                </div>

                <!-- CARDS DINÂMICOS DA API (COM DARK MODE) -->
                <div v-if="permissao === 'SuperAdmin'" class="bg-white p-5 rounded-xl shadow-md flex items-center space-x-4 dark:bg-gray-800 dark:border dark:border-gray-700">
                    <div class="flex-shrink-0"><div class="bg-indigo-100 p-3 rounded-full dark:bg-indigo-900/50"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-indigo-600 dark:text-indigo-300"><path d="M11.983 1.907a.75.75 0 0 0-1.966 0l-3.25 1.625a.75.75 0 0 0-.517.696V8.75a.75.75 0 0 0 .75.75h.03l.02-.001.02.001h.029l.015-.001.015.001H8.25a.75.75 0 0 0 .75-.75V5.13l2.5-1.25v3.62a.75.75 0 0 0 .75.75h.03l.02-.001.02.001h.029l.015-.001.015.001H12a.75.75 0 0 0 .75-.75V4.228a.75.75 0 0 0-.517-.696l-3.25-1.625Z" /><path d="m5.212 7.378.018-.009.018.009L7 8.216v2.534a.75.75 0 0 1-.75.75h-.03l-.02.001-.02-.001H6.151l-.015.001-.015-.001H6a.75.75 0 0 1-.75-.75V8.216l1.762-.838Z" /><path d="m12.988 7.378.018-.009.018.009L14.788 8.216v2.534a.75.75 0 0 1-.75.75h-.03l-.02.001-.02-.001h-.029l-.015.001-.015-.001H13.8a.75.75 0 0 1-.75-.75V8.216l1.762-.838Z" /><path d="M5.212 11.628.018-.01.018.01 1.762 12.46v2.534a.75.75 0 0 0 .75.75h.03l.02-.001.02.001h.029l.015-.001.015.001H3a.75.75 0 0 0 .75-.75V12.46l1.762-.838Z" /><path d="m12.988 11.628.018-.01.018.01L14.788 12.46v2.534a.75.75 0 0 0 .75.75h.03l.02-.001.02.001h.029l.015-.001.015.001H16.8a.75.75 0 0 0 .75-.75V12.46l1.762-.838Z" /></svg></div></div>
                    <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Imobiliárias</p><p class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ cards.total_imobiliarias }}</p></div>
                </div>
                <div v-if="permissao === 'SuperAdmin'" class="bg-white p-5 rounded-xl shadow-md flex items-center space-x-4 dark:bg-gray-800 dark:border dark:border-gray-700">
                    <div class="flex-shrink-0"><div class="bg-green-100 p-3 rounded-full dark:bg-green-900/50"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-green-600 dark:text-green-300"><path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.25 1.25 0 0 0 2.404-1.215 5.5 5.5 0 0 1 1.03-1.63 5.5 5.5 0 0 1 1.63-1.03 1.25 1.25 0 0 0-1.215-2.404 8.001 8.001 0 0 0-4.848 12.28c.433.64 1.2.985 1.996.985H10v-2.5a3.5 3.5 0 0 0-3.5-3.5H3.465ZM10.75 11.25a.75.75 0 0 0-1.5 0v2.5h-2.5a.75.75 0 0 0 0 1.5h2.5v2.5a.75.75 0 0 0 1.5 0v-2.5h2.5a.75.75 0 0 0 0-1.5h-2.5v-2.5Z" /><path d="M14.5 10.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM10 12.5a5.5 5.5 0 0 1 5.5 5.5v2.5h-2.136a8.002 8.002 0 0 0-12.28-4.848 1.25 1.25 0 0 0 1.215 2.404 5.5 5.5 0 0 1 1.63 1.03 5.5 5.5 0 0 1 1.03 1.63 1.25 1.25 0 0 0 2.404 1.215 8.001 8.001 0 0 0 2.671-.985V18a3.5 3.5 0 0 0-3.5-3.5H10v-2Z" /></svg></div></div>
                    <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Usuários</p><p class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ cards.total_usuarios_sistema }}</p></div>
                </div>

                <!-- Admin / Coordenador -->
                <div v-if="permissao === 'Admin' || permissao === 'Coordenador'" class="bg-white p-5 rounded-xl shadow-md flex items-center space-x-4 dark:bg-gray-800 dark:border dark:border-gray-700">
                    <div class="flex-shrink-0"><div class="bg-teal-100 p-3 rounded-full dark:bg-teal-900/50"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-teal-600 dark:text-teal-300"><path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h9.5A2.25 2.25 0 0 1 17 4.25v8.5A2.25 2.25 0 0 1 14.75 15h-3.191l-3.216 3.216a.75.75 0 0 1-1.06 0L4.06 15H3a2.25 2.25 0 0 1-2.25-2.25v-8.5Z" clip-rule="evenodd" /></svg></div></div>
                    <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total de Leads</p><p class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ cards.total_leads }}</p></div>
                </div>
                <div v-if="permissao === 'Admin' || permissao === 'Coordenador'" class="bg-white p-5 rounded-xl shadow-md flex items-center space-x-4 dark:bg-gray-800 dark:border dark:border-gray-700">
                    <div class="flex-shrink-0"><div class="bg-purple-100 p-3 rounded-full dark:bg-purple-900/50"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-purple-600 dark:text-purple-300"><path d="M13 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM18 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM13 15a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM13 13.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM4.5 10.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM16.5 10.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM8.5 13.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" /></svg></div></div>
                    <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Clientes</p><p class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ cards.total_clientes }}</p></div>
                </div>

                <!-- Corretor -->
                <div v-if="permissao === 'Corretor'" class="bg-white p-5 rounded-xl shadow-md flex items-center space-x-4 dark:bg-gray-800 dark:border dark:border-gray-700">
                    <div class="flex-shrink-0"><div class="bg-teal-100 p-3 rounded-full dark:bg-teal-900/50"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-teal-600 dark:text-teal-300"><path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h9.5A2.25 2.25 0 0 1 17 4.25v8.5A2.25 2.25 0 0 1 14.75 15h-3.191l-3.216 3.216a.75.75 0 0 1-1.06 0L4.06 15H3a2.25 2.25 0 0 1-2.25-2.25v-8.5Z" clip-rule="evenodd" /></svg></div></div>
                    <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Meus Leads</p><p class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ cards.meus_leads }}</p></div>
                </div>
                <div v-if="permissao === 'Corretor'" class="bg-white p-5 rounded-xl shadow-md flex items-center space-x-4 dark:bg-gray-800 dark:border dark:border-gray-700">
                    <div class="flex-shrink-0"><div class="bg-purple-100 p-3 rounded-full dark:bg-purple-900/50"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-purple-600 dark:text-purple-300"><path d="M13 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM18 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM13 15a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM13 13.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM4.5 10.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM16.5 10.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM8.5 13.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" /></svg></div></div>
                    <div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Meus Clientes</p><p class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ cards.meus_clientes }}</p></div>
                </div>
            </div>
        `
    });

    app.component('lista-tarefas', {
        props: ['tarefas', 'hoje'],
        methods: {
            isTarefaVencida(tarefa) { /* ... */ return tarefa.prazo < this.hoje && tarefa.status !== 'concluida'; },
            formatarPrazo(tarefa) { /* ... */ 
                if (!tarefa.prazo) return 'Sem prazo';
                const data = new Date(tarefa.prazo + 'T00:00:00'); // Corrige fuso horário
                if (tarefa.prazo === this.hoje) { return 'Hoje'; }
                const amanha = new Date(new Date(this.hoje).setDate(new Date(this.hoje).getDate() + 1)).toISOString().split('T')[0];
                if (tarefa.prazo === amanha) { return 'Amanhã'; }
                if (this.isTarefaVencida(tarefa)) {
                    return `Venceu em ${data.toLocaleDateString('pt-BR')}`;
                }
                return data.toLocaleDateString('pt-BR');
            },
            getPrazoClasses(tarefa) {
                if (this.isTarefaVencida(tarefa)) {
                    return 'text-red-600 bg-red-100 dark:text-red-400 dark:bg-red-900/50'; // Vencida
                }
                if (tarefa.prazo === this.hoje) {
                    return 'text-yellow-600 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/50'; // Hoje
                }
                return 'text-gray-500 bg-gray-100 dark:text-gray-400 dark:bg-gray-700'; // Normal
            }
        },
        template: `
            <div class="grid grid-cols-1 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-md dark:bg-gray-800 dark:border dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 dark:text-gray-200">Suas Tarefas Pendentes</h3>
                    
                    <div v-if="!tarefas || tarefas.length === 0" class="text-center py-10">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-10 h-10 text-green-500 mx-auto">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                        </svg>
                        <p class="mt-4 text-gray-500 dark:text-gray-400">Você está em dia! Nenhuma tarefa pendente.</p>
                    </div>

                    <ul v-else class="space-y-4">
                        <li v-for="tarefa in tarefas" :key="tarefa.id_tarefa" class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 dark:bg-gray-700/50 dark:border-gray-600">
                            <div class="flex items-center space-x-3 mb-2 sm:mb-0">
                                <span class="flex-shrink-0 w-3 h-3 rounded-full" :class="isTarefaVencida(tarefa) ? 'bg-red-500' : 'bg-blue-500'"></span>
                                <p class="text-gray-700 font-medium dark:text-gray-300">{{ tarefa.descricao }}</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span :class="getPrazoClasses(tarefa)" class="text-sm font-semibold px-2.5 py-0.5 rounded-full">
                                    {{ formatarPrazo(tarefa) }}
                                </span>
                                <a :href="'../tarefas/index.php?id=' + tarefa.id_tarefa" class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    Ver
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        `
    });

    app.mount('#app-dashboard');
</script>