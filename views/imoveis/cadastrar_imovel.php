<?php
// Inicia a sessão aqui também para poder ler as mensagens
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- O action deve apontar para o seu script de controller -->
<form action="../../controllers/ImovelController.php" method="POST" enctype="multipart/form-data" class="space-y-6 bg-white p-8 rounded-lg shadow-md">
    <input type="hidden" name="action" value="cadastrar">

    <div>
        <label for="titulo" class="block text-sm font-medium text-gray-700">Título</label>
        <input type="text" name="titulo" id="titulo" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div>
        <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
        <textarea name="descricao" id="descricao" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
            <select name="tipo" id="tipo" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Selecione</option>
                <option value="venda">Venda</option>
                <option value="locacao">Locação</option>
                <option value="temporada">Temporada</option>
                <option value="lancamento">Lançamento</option>
            </select>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" id="status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Selecione</option>
                <option value="disponivel">Disponível</option>
                <option value="reservado">Reservado</option>
                <option value="vendido">Vendido</option>
                <option value="indisponivel">Indisponível</option>
            </select>
        </div>
    </div>

    <div>
        <label for="preco" class="block text-sm font-medium text-gray-700">Preço (R$)</label>
        <input type="text" name="preco" id="preco" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="R$ 0,00">
    </div>

    <div>
        <label for="endereco" class="block text-sm font-medium text-gray-700">Endereço</label>
        <input type="text" name="endereco" id="endereco" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
            <input type="text" name="latitude" id="latitude" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
            <input type="text" name="longitude" id="longitude" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>

    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Imagens</label>
            <input type="file" name="imagens[]" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Vídeos</label>
            <input type="file" name="videos[]" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Documentos</label>
            <input type="file" name="documentos[]" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>
    </div>

    <div class="flex justify-end pt-4">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            Cadastrar Imóvel
        </button>
    </div>
</form>
<!-- A </div> extra que estava aqui foi removida -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const precoInput = document.getElementById('preco');
    const form = document.querySelector('form');

    /**
     * Formata um valor numérico para o padrão de moeda brasileiro (BRL).
     * Utiliza a API Intl.NumberFormat para uma formatação mais robusta e correta.
     * @param {string} value - O valor a ser formatado.
     * @returns {string} - O valor formatado como 'R$ 1.234,56' ou string vazia.
     */
    const formatCurrency = (value) => {
        if (!value) return '';

        // Remove todos os caracteres que não são dígitos.
        const cleanValue = String(value).replace(/\D/g, '');

        if (cleanValue === '') return '';

        // Converte o valor limpo (em centavos) para um número.
        const numberValue = parseInt(cleanValue, 10) / 100;

        // Formata o número para o padrão BRL.
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(numberValue);
    };

    if (precoInput) {
        // Formata o valor do input enquanto o usuário digita.
        precoInput.addEventListener('input', (e) => {
            e.target.value = formatCurrency(e.target.value);
        });
    }

    if (form) {
        // Antes de submeter o formulário, limpa a formatação do preço.
        form.addEventListener('submit', () => {
            if (precoInput) {
                // Remove a formatação (R$, pontos, espaços) e troca a vírgula por ponto
                // para enviar um valor numérico puro para o backend (ex: '1234.56').
                const numericValue = precoInput.value.replace(/[R$\s.]/g, '').replace(',', '.');
                precoInput.value = numericValue;
            }
        });
    }
});
</script>
