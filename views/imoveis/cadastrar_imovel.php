<?php
// Inicia a sessão aqui também para poder ler as mensagens
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// O id_imobiliaria pode ser passado via GET para já vir selecionado
$id_imobiliaria_selecionada = $_GET['id_imobiliaria'] ?? null;
?>

<div class="bg-gray-50 min-h-screen p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">

        <form action="../../controllers/ImovelController.php" method="POST" enctype="multipart/form-data" id="imovel-form">

            <!-- Cabeçalho da Página e Ações -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Cadastrar Novo Imóvel</h1>
                    <p class="text-gray-600 mt-1">Preencha os detalhes abaixo para adicionar um novo imóvel ao sistema.</p>
                </div>
                <div class="flex items-center gap-4 mt-4 sm:mt-0">
                    <a href="listar.php" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-semibold text-sm hover:bg-gray-100 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold text-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm hover:shadow-md">
                        Salvar Imóvel
                    </button>
                </div>
            </div>

            <input type="hidden" name="action" value="cadastrar">
            <?php if ($id_imobiliaria_selecionada): ?>
                <input type="hidden" name="id_imobiliaria" value="<?= htmlspecialchars($id_imobiliaria_selecionada) ?>">
            <?php endif; ?>


            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Coluna Esquerda: Detalhes do Imóvel -->
                <div class="lg:col-span-2 bg-white p-8 rounded-xl shadow-md space-y-8">

                    <!-- Seção: Informações Principais -->
                    <fieldset>
                        <legend class="text-xl font-semibold text-gray-800 border-b pb-3 mb-6">Detalhes do Imóvel</legend>
                        <div class="space-y-6">
                            <div>
                                <label for="titulo" class="block text-sm font-medium text-gray-700">Título *</label>
                                <input type="text" name="titulo" id="titulo" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="preco" class="block text-sm font-medium text-gray-700">Preço (R$) *</label>
                                    <input type="text" name="preco" id="preco" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="R$ 0,00">
                                </div>
                                <div>
                                    <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo de Anúncio *</label>
                                    <select name="tipo" id="tipo" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Selecione</option>
                                        <option value="venda">Venda</option>
                                        <option value="locacao">Locação</option>
                                        <option value="temporada">Temporada</option>
                                        <option value="lancamento">Lançamento</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
                                <textarea name="descricao" id="descricao" rows="5" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Seção: Localização -->
                    <fieldset>
                         <legend class="text-xl font-semibold text-gray-800 border-b pb-3 mb-6">Localização</legend>
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
                </div>

                <!-- Coluna Direita: Mídia e Status -->
                <div class="space-y-8">
                    <div class="bg-white p-8 rounded-xl shadow-md">
                        <fieldset>
                            <legend class="text-xl font-semibold text-gray-800 border-b pb-3 mb-6">Status do Anúncio</legend>
                            <label for="status" class="block text-sm font-medium text-gray-700">Disponibilidade *</label>
                            <select name="status" id="status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="disponivel" selected>Disponível</option>
                                <option value="reservado">Reservado</option>
                                <option value="vendido">Vendido</option>
                                <option value="indisponivel">Indisponível</option>
                            </select>
                        </fieldset>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-md">
                        <fieldset>
                            <legend class="text-xl font-semibold text-gray-800 border-b pb-3 mb-6">Mídias do Imóvel</legend>
                            <div class="space-y-4">
                                <div>
                                    <label for="imagens" class="block text-sm font-medium text-gray-700">Imagens</label>
                                    <div class="mt-1">
                                        <input type="file" name="imagens[]" id="imagens" multiple accept="image/*" class="sr-only">
                                        <label for="imagens" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            <span id="imagens-label">Selecionar Imagens</span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label for="videos" class="block text-sm font-medium text-gray-700">Vídeos</label>
                                    <div class="mt-1">
                                        <input type="file" name="videos[]" id="videos" multiple accept="video/*" class="sr-only">
                                        <label for="videos" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                            <span id="videos-label">Selecionar Vídeos</span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label for="documentos" class="block text-sm font-medium text-gray-700">Documentos</label>
                                    <div class="mt-1">
                                        <input type="file" name="documentos[]" id="documentos" multiple accept=".pdf,.doc,.docx,.xls,.xlsx" class="sr-only">
                                        <label for="documentos" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                            <span id="documentos-label">Selecionar Documentos</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const precoInput = document.getElementById('preco');
    const form = document.getElementById('imovel-form');

    const formatCurrency = (value) => {
        if (!value) return '';
        const cleanValue = String(value).replace(/\D/g, '');
        if (cleanValue === '') return '';
        const numberValue = parseInt(cleanValue, 10) / 100;
        return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(numberValue);
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
    
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, '');
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            document.getElementById('endereco').value = data.logradouro;
                            document.getElementById('bairro').value = data.bairro;
                            document.getElementById('cidade').value = data.localidade;
                            document.getElementById('estado').value = data.uf;
                            document.getElementById('numero').focus(); 
                        } else {
                            alert('CEP não encontrado.');
                        }
                    })
                    .catch(error => console.error('Erro ao buscar CEP:', error));
            }
        });
    }

    function setupFileInput(inputId, labelId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        input.addEventListener('change', function(e) {
            const label = document.getElementById(labelId);
            const fileCount = e.target.files.length;
            if (fileCount === 1) {
                label.textContent = e.target.files[0].name;
            } else if (fileCount > 1) {
                label.textContent = `${fileCount} arquivos selecionados`;
            }
        });
    }

    setupFileInput('imagens', 'imagens-label');
    setupFileInput('videos', 'videos-label');
    setupFileInput('documentos', 'documentos-label');
});
</script>
