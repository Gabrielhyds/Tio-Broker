<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$id_imobiliaria_selecionada = $_GET['id_imobiliaria'] ?? null;
?>

<div class="min-h-screen bg-gradient-to-br from-gray-100 to-white p-6">
    <div class="max-w-6xl mx-auto bg-white rounded-3xl shadow-2xl p-8">
        
        <h1 class="text-4xl font-extrabold text-gray-800 mb-4 text-center">🚀 Novo Empreendimento</h1>
        <p class="text-gray-500 mb-8 text-center">Crie e visualize seu empreendimento de forma interativa e moderna.</p>

        <form action="../../controllers/EmpreendimentoController.php" method="POST" enctype="multipart/form-data" id="empreendimento-form">
            <input type="hidden" name="action" value="cadastrar">
            <?php if ($id_imobiliaria_selecionada): ?>
                <input type="hidden" name="id_imobiliaria" value="<?= htmlspecialchars($id_imobiliaria_selecionada) ?>">
            <?php endif; ?>

            <!-- Wizard de Etapas -->
            <div id="wizard" class="space-y-8">

                <!-- Etapa 1: Informações Gerais -->
                <section class="wizard-step">
                    <h2 class="text-2xl font-semibold text-gray-700 mb-6">1. Informações Gerais</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label class="block font-medium text-gray-700">Nome do Empreendimento *</label>
                            <input type="text" name="nome" required class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3">
                        </div>

                        <div>
                            <label class="block font-medium text-gray-700">Categoria *</label>
                            <select name="categoria" required class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3">
                                <option value="imobiliario">Imobiliário</option>
                                <option value="automotivo">Automotivo</option>
                                <option value="franquia">Franquia</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-medium text-gray-700">Status *</label>
                            <select name="status" required class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3">
                                <option value="disponivel">Disponível</option>
                                <option value="planejamento">Planejamento</option>
                                <option value="em_andamento">Em Andamento</option>
                                <option value="concluido">Concluído</option>
                                <option value="encerrado">Encerrado</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block font-medium text-gray-700">Descrição</label>
                            <textarea name="descricao" rows="4" class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3"></textarea>
                        </div>

                    </div>
                </section>

                <!-- Etapa 2: Localização -->
                <section class="wizard-step hidden">
                    <h2 class="text-2xl font-semibold text-gray-700 mb-6">2. Localização</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block font-medium text-gray-700">CEP</label>
                            <input type="text" name="cep" id="cep" class="mt-2 w-full rounded-xl border-gray-300 shadow-sm p-3">
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700">Cidade</label>
                            <input type="text" name="cidade" id="cidade" class="mt-2 w-full rounded-xl border-gray-300 shadow-sm p-3">
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700">Estado</label>
                            <input type="text" name="estado" id="estado" class="mt-2 w-full rounded-xl border-gray-300 shadow-sm p-3">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block font-medium text-gray-700">Endereço</label>
                            <input type="text" name="endereco" id="endereco" class="mt-2 w-full rounded-xl border-gray-300 shadow-sm p-3">
                        </div>
                    </div>
                </section>

                <!-- Etapa 3: Mídias -->
                <section class="wizard-step hidden">
                    <h2 class="text-2xl font-semibold text-gray-700 mb-6">3. Mídias</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <!-- Imagens -->
                        <div class="bg-blue-50 rounded-xl p-4 text-center">
                            <label class="block font-medium text-gray-700 mb-2">Imagens</label>
                            <input type="file" name="imagens[]" id="imagens" multiple accept="image/*" class="hidden">
                            <button type="button" onclick="document.getElementById('imagens').click()" class="w-full py-2 px-4 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">Selecionar Imagens</button>
                            <div id="imagens-preview" class="mt-4 grid grid-cols-3 gap-2"></div>
                        </div>

                        <!-- Vídeos -->
                        <div class="bg-green-50 rounded-xl p-4 text-center">
                            <label class="block font-medium text-gray-700 mb-2">Vídeos</label>
                            <input type="file" name="videos[]" id="videos" multiple accept="video/*" class="hidden">
                            <button type="button" onclick="document.getElementById('videos').click()" class="w-full py-2 px-4 bg-green-600 text-white rounded-xl hover:bg-green-700 transition">Selecionar Vídeos</button>
                        </div>

                        <!-- Documentos -->
                        <div class="bg-yellow-50 rounded-xl p-4 text-center">
                            <label class="block font-medium text-gray-700 mb-2">Documentos</label>
                            <input type="file" name="documentos[]" id="documentos" multiple accept=".pdf,.doc,.docx,.xls,.xlsx" class="hidden">
                            <button type="button" onclick="document.getElementById('documentos').click()" class="w-full py-2 px-4 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition">Selecionar Documentos</button>
                        </div>

                    </div>
                </section>

            </div>

            <!-- Botões de Navegação -->
            <div class="flex justify-between mt-8">
                <button type="button" id="prevBtn" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-xl hover:bg-gray-300 transition hidden">Anterior</button>
                <button type="button" id="nextBtn" class="bg-blue-600 text-white px-6 py-2 rounded-xl hover:bg-blue-700 transition">Próximo</button>
                <button type="submit" id="submitBtn" class="bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition hidden">Salvar Empreendimento</button>
            </div>

        </form>
    </div>
</div>

<script>
// Wizard JS
const steps = document.querySelectorAll('.wizard-step');
let currentStep = 0;

const showStep = (index) => {
    steps.forEach((step, i) => step.classList.toggle('hidden', i !== index));
    document.getElementById('prevBtn').classList.toggle('hidden', index === 0);
    document.getElementById('nextBtn').classList.toggle('hidden', index === steps.length-1);
    document.getElementById('submitBtn').classList.toggle('hidden', index === steps.length-1);
}

document.getElementById('nextBtn').addEventListener('click', () => {
    if (currentStep < steps.length - 1) {
        currentStep++;
        showStep(currentStep);
    }
});

document.getElementById('prevBtn').addEventListener('click', () => {
    if (currentStep > 0) {
        currentStep--;
        showStep(currentStep);
    }
});

showStep(currentStep);

// Pré-visualização de imagens
const imagensInput = document.getElementById('imagens');
const imagensPreview = document.getElementById('imagens-preview');
imagensInput.addEventListener('change', () => {
    imagensPreview.innerHTML = '';
    Array.from(imagensInput.files).forEach(file => {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.className = 'w-full h-24 object-cover rounded-lg';
        imagensPreview.appendChild(img);
    });
});
</script>

