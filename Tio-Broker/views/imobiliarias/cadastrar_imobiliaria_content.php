<!-- Contêiner principal do formulário, com estilo de cartão. -->
<div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow-md">
    <!-- Título do formulário. -->
    <h2 class="text-2xl font-bold mb-6 text-center flex items-center justify-center gap-2">
        <i class="fas fa-building-circle-plus text-blue-600"></i>
        Nova Imobiliária
    </h2>

    <!-- Início do formulário. `onsubmit` chama uma função de validação em JavaScript antes de enviar. -->
    <form action="../../controllers/ImobiliariaController.php" method="POST" onsubmit="validarFormulario(event)">
        <!-- Campo oculto para indicar ao controller que a ação é 'cadastrar'. -->
        <input type="hidden" name="action" value="cadastrar">

        <!-- NOVO: Seletor de Tipo de Pessoa -->
        <div class="mb-4">
            <label for="tipo_pessoa" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Pessoa</label>
            <select id="tipo_pessoa" name="tipo_pessoa" onchange="alternarDocumento()" class="w-full border rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="J">Pessoa Jurídica (CNPJ)</option>
                <option value="F">Pessoa Física (CPF)</option>
            </select>
        </div>

        <!-- Campo de Documento (dinâmico para CPF ou CNPJ) -->
        <div class="mb-4">
            <label id="label_documento" for="documento" class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
            <div class="relative">
                <!-- O 'name' foi alterado para 'documento' para ser genérico. -->
                <input type="text" id="documento" name="documento"
                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="00.000.000/0000-00"
                    maxlength="18"
                    required
                    onkeypress="apenasNumeros(event)"
                    oninput="formatarDocumento(this)"
                    onblur="buscarDadosCNPJ(this)">
                <!-- Ícone dentro do campo de input (também dinâmico). -->
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i id="icone_documento" class="fas fa-barcode text-gray-400"></i>
                </div>
            </div>
        </div>

        <!-- Campo Nome -->
        <div class="mb-6">
            <label id="label_nome" for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome da Imobiliária / Razão Social</label>
            <div class="relative">
                <input type="text" id="nome" name="nome"
                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="Ex: Imobiliária Exemplo Ltda."
                    required>
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-building text-gray-400"></i>
                </div>
            </div>
        </div>

        <!-- Botões de ação do formulário -->
        <div class="flex justify-between">
            <a href="listar_imobiliaria.php" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Voltar
            </a>
            <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                <i class="fas fa-save mr-2"></i> Cadastrar
            </button>
        </div>
    </form>
</div>
<!-- Modal para exibir alertas ao usuário. Inicialmente oculto. -->
<div id="modalAlerta" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
        <h2 class="text-xl font-semibold mb-4 text-red-600"><i class="fas fa-exclamation-triangle mr-2"></i> Alerta</h2>
        <p id="modalMensagem" class="text-gray-700 mb-6">Mensagem do alerta.</p>
        <div class="text-end">
            <button onclick="fecharModal()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Ok
            </button>
        </div>
    </div>
</div>

<!-- Scripts JavaScript para interatividade do formulário. -->
<script>
    // Função para permitir que o usuário digite apenas números no campo.
    function apenasNumeros(event) {
        if (!/\d/.test(event.key)) event.preventDefault();
    }

    // Função para alternar entre os campos de CPF e CNPJ.
    function alternarDocumento() {
        const tipo = document.getElementById('tipo_pessoa').value;
        const labelDoc = document.getElementById('label_documento');
        const inputDoc = document.getElementById('documento');
        const iconeDoc = document.getElementById('icone_documento');
        const labelNome = document.getElementById('label_nome');

        inputDoc.value = ''; // Limpa o campo na troca
        document.getElementById('nome').value = ''; // Limpa o nome também

        if (tipo === 'F') { // Pessoa Física
            labelDoc.textContent = 'CPF';
            labelNome.textContent = 'Nome Completo';
            inputDoc.placeholder = '000.000.000-00';
            inputDoc.maxLength = 14;
            inputDoc.onblur = null; // Remove a busca de dados da API
            iconeDoc.className = "fas fa-id-card text-gray-400";
        } else { // Pessoa Jurídica
            labelDoc.textContent = 'CNPJ';
            labelNome.textContent = 'Nome da Imobiliária / Razão Social';
            inputDoc.placeholder = '00.000.000/0000-00';
            inputDoc.maxLength = 18;
            inputDoc.onblur = function() { buscarDadosCNPJ(this); }; // Adiciona a busca de volta
            iconeDoc.className = "fas fa-barcode text-gray-400";
        }
    }

    // Função para aplicar a máscara correta (CPF ou CNPJ) enquanto o usuário digita.
    function formatarDocumento(campo) {
        const tipo = document.getElementById('tipo_pessoa').value;
        let valor = campo.value.replace(/\D/g, '');

        if (tipo === 'F') {
            valor = valor.slice(0, 11);
            valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
            valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
            valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        } else {
            valor = valor.slice(0, 14);
            valor = valor.replace(/^(\d{2})(\d)/, "$1.$2");
            valor = valor.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
            valor = valor.replace(/\.(\d{3})(\d)/, ".$1/$2");
            valor = valor.replace(/(\d{4})(\d)/, "$1-$2");
        }
        campo.value = valor;
    }

    // Função para buscar dados da empresa a partir do CNPJ (só para PJ).
    function buscarDadosCNPJ(campo) {
        const cnpj = campo.value.replace(/\D/g, '');
        if (cnpj.length === 14) {
            const nomeCampo = document.getElementById('nome');
            nomeCampo.value = "Buscando...";
            nomeCampo.readOnly = true;

            fetch(`../../controllers/BuscarCNPJController.php?cnpj=${cnpj}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === "OK") {
                        nomeCampo.value = data.nome;
                    } else {
                        nomeCampo.value = "";
                        abrirModal("CNPJ não encontrado. Você pode preencher o nome manualmente.");
                    }
                })
                .catch(() => {
                    nomeCampo.value = "";
                    abrirModal("Erro ao buscar informações do CNPJ. Preencha manualmente.");
                })
                .finally(() => {
                    nomeCampo.readOnly = false;
                });
        }
    }

    // Função para validar o formulário antes do envio.
    function validarFormulario(event) {
        const tipo = document.getElementById('tipo_pessoa').value;
        const doc = document.getElementById('documento').value.replace(/\D/g, '');
        const nome = document.getElementById('nome').value.trim();

        if (tipo === 'F') {
            if (doc.length !== 11) {
                event.preventDefault();
                abrirModal("CPF inválido! Deve conter 11 dígitos.");
                return;
            }
        } else { // 'J'
            if (doc.length !== 14) {
                event.preventDefault();
                abrirModal("CNPJ inválido! Deve conter 14 dígitos.");
                return;
            }
        }

        if (nome === "Buscando..." || nome === "") {
            event.preventDefault();
            abrirModal("Por favor, preencha o campo de nome.");
            return;
        }
    }

    // Funções do Modal de Alerta
    function abrirModal(mensagem) {
        document.getElementById('modalMensagem').textContent = mensagem;
        document.getElementById('modalAlerta').classList.remove('hidden');
    }

    function fecharModal() {
        document.getElementById('modalAlerta').classList.add('hidden');
    }
</script>
