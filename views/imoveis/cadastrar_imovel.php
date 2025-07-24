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

    <!-- ✅ INÍCIO DA SEÇÃO DE ENDEREÇO REFATORADA -->
    <fieldset class="border-t pt-6">
        <legend class="text-lg font-medium text-gray-900 mb-4">Localização</legend>
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-1">
                    <label for="cep" class="block text-sm font-medium text-gray-700">CEP</label>
                    <input type="text" name="cep" id="cep" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="00000-000">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-3">
                    <label for="endereco" class="block text-sm font-medium text-gray-700">Logradouro (Rua, Av.)</label>
                    <input type="text" name="endereco" id="endereco" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50">
                </div>
                <div>
                    <label for="numero" class="block text-sm font-medium text-gray-700">Número</label>
                    <input type="text" name="numero" id="numero" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="bairro" class="block text-sm font-medium text-gray-700">Bairro</label>
                    <input type="text" name="bairro" id="bairro" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50">
                </div>
                <div>
                    <label for="cidade" class="block text-sm font-medium text-gray-700">Cidade</label>
                    <input type="text" name="cidade" id="cidade" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50">
                </div>
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700">Estado (UF)</label>
                    <input type="text" name="estado" id="estado" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50">
                </div>
            </div>

            <div>
                <label for="complemento" class="block text-sm font-medium text-gray-700">Complemento</label>
                <input type="text" name="complemento" id="complemento" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Ex: Apto 101, Bloco B">
            </div>
        </div>
    </fieldset>
    <!-- ✅ FIM DA SEÇÃO DE ENDEREÇO REFATORADA -->


    <div class="space-y-4 border-t pt-6">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const precoInput = document.getElementById('preco');
    const form = document.querySelector('form');

    // --- LÓGICA DE FORMATAÇÃO DE PREÇO ---
    const formatCurrency = (value) => {
        if (!value) return '';
        const cleanValue = String(value).replace(/\D/g, '');
        if (cleanValue === '') return '';
        const numberValue = parseInt(cleanValue, 10) / 100;
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(numberValue);
    };

    if (precoInput) {
        precoInput.addEventListener('input', (e) => {
            e.target.value = formatCurrency(e.target.value);
        });
    }

    if (form) {
        form.addEventListener('submit', () => {
            if (precoInput) {
                const numericValue = precoInput.value.replace(/[R$\s.]/g, '').replace(',', '.');
                precoInput.value = numericValue;
            }
        });
    }
    
    // --- ✅ NOVA LÓGICA PARA BUSCA DE ENDEREÇO VIA CEP ---
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, ''); // Remove caracteres não numéricos
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            document.getElementById('endereco').value = data.logradouro;
                            document.getElementById('bairro').value = data.bairro;
                            document.getElementById('cidade').value = data.localidade;
                            document.getElementById('estado').value = data.uf;
                            // Foca no campo de número para o usuário preencher
                            document.getElementById('numero').focus(); 
                        } else {
                            alert('CEP não encontrado.');
                        }
                    })
                    .catch(error => console.error('Erro ao buscar CEP:', error));
            }
        });
    }
});
</script>
