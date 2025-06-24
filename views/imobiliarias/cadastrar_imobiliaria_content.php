<!-- Bloco PHP para exibir uma mensagem de sucesso, se existir na sessão. -->
<?php if (isset($_SESSION['sucesso'])): ?>
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
        <?= htmlspecialchars($_SESSION['sucesso']); // Exibe a mensagem de forma segura.
        unset($_SESSION['sucesso']); // Remove a mensagem da sessão para não ser exibida novamente. 
        ?>
    </div>
<?php endif; ?>

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

        <!-- Campo CNPJ -->
        <div class="mb-4">
            <label for="cnpj" class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
            <div class="relative">
                <!-- Campo de texto para o CNPJ com eventos para formatação e busca automática. -->
                <input type="text" id="cnpj" name="cnpj"
                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="00.000.000/0000-00"
                    maxlength="18"
                    required
                    onkeypress="apenasNumeros(event)"
                    oninput="formatarCNPJ(this)"
                    onblur="buscarDadosCNPJ(this)">
                <!-- Ícone dentro do campo de input. -->
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-barcode text-gray-400"></i>
                </div>
            </div>
        </div>

        <!-- Campo Nome -->
        <div class="mb-6">
            <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome da Imobiliária</label>
            <div class="relative">
                <input type="text" id="nome" name="nome"
                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="Ex: Imobiliária Exemplo Ltda."
                    required>
                <!-- Ícone dentro do campo de input. -->
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

    // Função para aplicar a máscara de CNPJ (00.000.000/0000-00) enquanto o usuário digita.
    function formatarCNPJ(campo) {
        let cnpj = campo.value.replace(/\D/g, '').slice(0, 14); // Remove não-dígitos e limita a 14.
        cnpj = cnpj.replace(/^(\d{2})(\d)/, "$1.$2");
        cnpj = cnpj.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
        cnpj = cnpj.replace(/\.(\d{3})(\d)/, ".$1/$2");
        cnpj = cnpj.replace(/(\d{4})(\d)/, "$1-$2");
        campo.value = cnpj;
    }

    // Função para buscar dados da empresa a partir do CNPJ usando uma API externa.
    function buscarDadosCNPJ(campo) {
        const cnpj = campo.value.replace(/\D/g, ''); // Limpa o CNPJ.

        if (cnpj.length === 14) {
            const nomeCampo = document.getElementById('nome');
            nomeCampo.value = "Buscando..."; // Feedback visual para o usuário.
            nomeCampo.readOnly = true; // Impede a edição enquanto a busca ocorre.

            // Faz uma requisição para o controller que consulta a API de CNPJ.
            fetch(`../../controllers/BuscarCNPJController.php?cnpj=${cnpj}`)
                .then(response => response.json()) // Converte a resposta para JSON.
                .then(data => {
                    if (data.status === "OK") {
                        nomeCampo.value = data.nome; // Preenche o nome da empresa se encontrado.
                    } else {
                        nomeCampo.value = ""; // Limpa o campo se não encontrar.
                        abrirModal("CNPJ não encontrado. Você pode preencher o nome manualmente.");
                    }
                })
                .catch(() => {
                    nomeCampo.value = ""; // Limpa o campo em caso de erro.
                    abrirModal("Erro ao buscar informações do CNPJ. Preencha manualmente.");
                })
                .finally(() => {
                    nomeCampo.readOnly = false; // Libera o campo para edição após a busca.
                });
        }
    }

    // Função para validar o formulário antes do envio.
    function validarFormulario(event) {
        const cnpj = document.getElementById('cnpj').value.replace(/\D/g, '');
        const nome = document.getElementById('nome').value.trim();

        if (cnpj.length !== 14) {
            event.preventDefault(); // Impede o envio do formulário.
            abrirModal("CNPJ inválido! Deve conter exatamente 14 dígitos.");
            return;
        }

        if (nome === "Buscando..." || nome === "") {
            event.preventDefault();
            abrirModal("Por favor, preencha um nome para a imobiliária.");
            return;
        }

        // Se tudo estiver correto, o formulário é enviado.
    }

    // Função para abrir o modal de alerta com uma mensagem específica.
    function abrirModal(mensagem) {
        document.getElementById('modalMensagem').textContent = mensagem;
        document.getElementById('modalAlerta').classList.remove('hidden');
    }

    // Função para fechar o modal de alerta.
    function fecharModal() {
        document.getElementById('modalAlerta').classList.add('hidden');
    }
</script>