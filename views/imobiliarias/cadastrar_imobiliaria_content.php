<?php if (isset($_SESSION['sucesso'])): ?>
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
        <?= htmlspecialchars($_SESSION['sucesso']);
        unset($_SESSION['sucesso']); ?>
    </div>
<?php endif; ?>

<div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-center flex items-center justify-center gap-2">
        <i class="fas fa-building-circle-plus text-blue-600"></i>
        Nova Imobiliária
    </h2>

    <form action="../../controllers/ImobiliariaController.php" method="POST" onsubmit="validarFormulario(event)">
        <input type="hidden" name="action" value="cadastrar">

        <!-- CNPJ -->
        <div class="mb-4">
            <label for="cnpj" class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
            <div class="relative">
                <input type="text" id="cnpj" name="cnpj"
                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="00.000.000/0000-00"
                    maxlength="18"
                    required
                    onkeypress="apenasNumeros(event)"
                    oninput="formatarCNPJ(this)"
                    onblur="buscarDadosCNPJ(this)">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-barcode text-gray-400"></i>
                </div>
            </div>
        </div>

        <!-- Nome -->
        <div class="mb-6">
            <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome da Imobiliária</label>
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

        <!-- Botões -->
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
<!-- Modal de Alerta -->
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

<script>
    let cnpjValido = false; // Controle global do status do CNPJ

    function apenasNumeros(event) {
        if (!/\d/.test(event.key)) event.preventDefault();
    }

    function formatarCNPJ(campo) {
        let cnpj = campo.value.replace(/\D/g, '').slice(0, 14);
        cnpj = cnpj.replace(/^(\d{2})(\d)/, "$1.$2");
        cnpj = cnpj.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
        cnpj = cnpj.replace(/\.(\d{3})(\d)/, ".$1/$2");
        cnpj = cnpj.replace(/(\d{4})(\d)/, "$1-$2");
        campo.value = cnpj;
    }

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

    function validarFormulario(event) {
        const cnpj = document.getElementById('cnpj').value.replace(/\D/g, '');
        const nome = document.getElementById('nome').value.trim();

        if (cnpj.length !== 14) {
            event.preventDefault();
            abrirModal("CNPJ inválido! Deve conter exatamente 14 dígitos.");
            return;
        }

        if (nome === "Buscando..." || nome === "") {
            event.preventDefault();
            abrirModal("Por favor, preencha um nome para a imobiliária.");
            return;
        }

        // Tudo ok — deixa enviar
    }

    function abrirModal(mensagem) {
        document.getElementById('modalMensagem').textContent = mensagem;
        document.getElementById('modalAlerta').classList.remove('hidden');
    }

    function fecharModal() {
        document.getElementById('modalAlerta').classList.add('hidden');
    }
</script>