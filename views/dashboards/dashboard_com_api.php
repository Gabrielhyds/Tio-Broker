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
                <!-- ... (Ícone) ... -->
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
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Clientes por Status</h3>
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
<!-- ETAPA 2: JavaScript (Agora busca dados da API)                          -->
<!-- ========================================================================= -->

<!-- MUDANÇA: Não há mais script "dadosIniciais" injetado pelo PHP -->
 
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script><script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const { createApp } = Vue;
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
            renderizarGraficos() {
                // ... (código chart.js) ...
                console.log("Renderizando gráficos com dados da API...");
                // (O código completo do Chart.js que estava no arquivo anterior vai aqui)
                // Ex: new Chart(ctx, { ... data: this.dadosGraficoFunil ... })
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
    // DEFINIÇÃO DOS COMPONENTES (Exatamente como antes)
    // =====================================================================
    // Os componentes não mudam NADA. Eles são "agnósticos"
    // sobre como o pai (a 'app') obteve os dados.
    
    app.component('grid-cards', {
        props: ['cards', 'permissao', 'totalTarefas'],
        // (template HTML do grid-cards ... )
        template: `
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Card de Perfil (Fixo) -->
                <div class="bg-white p-5 rounded-xl shadow-md">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Perfil de Acesso</p>
                        <p class="text-xl font-bold text-blue-600">{{ permissao }}</p>
                    </div>
                </div>
                <!-- ... (resto do template do grid-cards) ... -->
            </div>
        `
    });

    app.component('lista-tarefas', {
        props: ['tarefas', 'hoje'],
        methods: {
            isTarefaVencida(tarefa) { /* ... */ },
            formatarPrazo(tarefa) { /* ... */ },
            getPrazoClasses(tarefa) { /* ... */ }
        },
        // (template HTML da lista-tarefas ... )
        template: `
            <div class="grid grid-cols-1 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Suas Tarefas</h3>
                    <!-- ... (resto do template da lista-tarefas) ... -->
                </div>
            </div>
        `
    });

    // Finalmente, monta o app
    app.mount('#app-dashboard');
</script>