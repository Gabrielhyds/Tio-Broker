<?php
// Este código deve estar no início do seu ficheiro `mostrar.php`
// As variáveis $imovel, $imagens, $videos, $documentos, $plantas, $caracteristicas já devem ter sido carregadas.

// Lógica para formatar o endereço completo para exibição e para a query do mapa.
$enderecoArray = array_filter([
    $imovel['endereco'],
    $imovel['numero'],
    $imovel['bairro'],
    $imovel['cidade'],
    $imovel['estado']
]);
$enderecoCompleto = implode(', ', $enderecoArray);
$mapQuery = urlencode($enderecoCompleto . ', ' . $imovel['cep']);


// Define a imagem principal
$imagemPrincipal = !empty($imagens) ? BASE_URL . ltrim($imagens[0]['caminho'], '/') : BASE_URL . 'assets/img/no-image.jpg';

// Verifica se o imóvel é um cadastro recente (últimos 7 dias)
$ehNovo = (strtotime($imovel['data_cadastro']) >= strtotime('-7 days'));
?>

<!-- === NOVOS ARQUIVOS CSS PARA O CARROSSEL E LIGHTBOX === -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
<style>
    /* Pequenos ajustes para os botões de navegação do Swiper */
    .swiper-button-next, .swiper-button-prev {
        color: #ffffff;
        background-color: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        width: 44px;
        height: 44px;
    }
    .swiper-button-next:after, .swiper-button-prev:after {
        font-size: 18px;
        font-weight: bold;
    }
</style>

<div class="max-w-7xl mx-auto p-4 md:p-6 lg:p-8">
    <!-- Botão Voltar -->
    <a href="listar.php" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 transition-colors mb-6 text-sm font-semibold">
        <i class="bi bi-arrow-left-circle"></i>
        Voltar para a listagem
    </a>

    <!-- Layout Principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">

        <!-- Coluna Principal de Conteúdo (Esquerda) -->
        <div class="lg:col-span-2 space-y-10">
            <!-- Cabeçalho do Imóvel -->
            <div>
                <h1 class="text-4xl font-bold text-gray-900 tracking-tight"><?= htmlspecialchars($imovel['titulo']) ?></h1>
                <p class="text-lg text-gray-500 mt-2 flex items-center gap-2">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span><?= htmlspecialchars($enderecoCompleto) ?></span>
                </p>
            </div>

            <!-- Imagem Principal -->
            <div class="relative rounded-2xl overflow-hidden shadow-lg">
                <img src="<?= htmlspecialchars($imagemPrincipal) ?>" alt="Foto principal de <?= htmlspecialchars($imovel['titulo']) ?>" class="w-full h-96 object-cover">
                <?php if ($ehNovo): ?>
                    <span class="absolute top-4 left-4 bg-green-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">NOVO</span>
                <?php endif; ?>
            </div>

            <!-- Seção "Sobre este imóvel" -->
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-3">Sobre este imóvel</h2>
                <div class="prose max-w-none text-gray-600 leading-relaxed">
                    <?= nl2br(htmlspecialchars($imovel['descricao'])) ?>
                </div>
            </div>

            <!-- === SEÇÃO DA GALERIA DE IMAGENS ATUALIZADA PARA CARROSSEL === -->
            <?php if (!empty($imagens) && count($imagens) > 1): ?>
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-3">Galeria de Imagens</h2>
                <!-- Swiper -->
                <div class="swiper rounded-xl">
                    <div class="swiper-wrapper">
                        <!-- Slides -->
                        <?php foreach ($imagens as $img): ?>
                            <div class="swiper-slide">
                                <a href="<?= BASE_URL . ltrim($img['caminho'], '/') ?>" data-fancybox="gallery" data-caption="Galeria: <?= htmlspecialchars($imovel['titulo']) ?>">
                                    <img src="<?= BASE_URL . ltrim($img['caminho'], '/') ?>" alt="Galeria do imóvel" class="w-full h-64 object-cover cursor-pointer">
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Botões de Navegação -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <!-- Paginação -->
                    <div class="swiper-pagination"></div>
                </div>
            </div>
            <?php endif; ?>
            <!-- === FIM DA SEÇÃO ATUALIZADA === -->

            <!-- Seção "Localização e Mapa" -->
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-3">Localização</h2>
                <iframe
                    src="https://maps.google.com/maps?q=<?= $mapQuery ?>&z=16&output=embed"
                    class="w-full h-80 rounded-xl shadow-md border"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    allowfullscreen>
                </iframe>
            </div>

            <!-- Vídeos e Documentos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <?php if (!empty($videos)): ?>
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-3">Vídeos</h2>
                        <div class="space-y-4">
                            <?php foreach ($videos as $vid): ?>
                                <video controls class="w-full rounded-lg shadow" preload="metadata">
                                    <source src="<?= BASE_URL . ltrim($vid['caminho'], '/') ?>" type="video/mp4">
                                    Seu navegador não suporta o elemento de vídeo.
                                </video>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($documentos)): ?>
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-3">Documentos</h2>
                        <div class="space-y-2">
                            <?php foreach ($documentos as $doc): ?>
                                <a href="<?= BASE_URL . ltrim($doc['caminho'], '/') ?>" target="_blank" class="flex items-center gap-3 p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors border">
                                    <i class="bi bi-file-earmark-text-fill text-blue-600 text-xl"></i>
                                    <span class="text-sm font-medium text-gray-700 truncate"><?= htmlspecialchars(basename($doc['caminho'])) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- SEÇÃO DE FEEDBACK DE VISITAS (Inalterada) -->
           <div id="visitas-feedback-section">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-3">Histórico de Visitas e Feedbacks</h2>
                <div id="visitas-list" class="space-y-4">
                    <div class="text-center p-4 text-gray-500"><i class="bi bi-arrow-clockwise animate-spin text-2xl"></i><p>Carregando histórico...</p></div>
                </div>
            </div>

        </div>
        
        <!-- Coluna Lateral de Resumo (Direita) (Inalterada) -->
        <div class="lg:col-span-1">
            <div class="lg:sticky top-8 space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-lg border">
                    <p class="text-gray-500 text-sm">Preço de Venda</p>
                    <p class="text-4xl font-bold text-gray-900 mt-1">R$ <?= number_format($imovel['preco'], 2, ',', '.') ?></p>
                    
                    <div class="mt-6 space-y-3 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium">Tipo de Anúncio</span>
                            <span class="font-bold text-gray-800"><?= ucfirst($imovel['tipo']) ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium">Status</span>
                            <span class="font-bold text-white text-xs px-2 py-1 rounded-full <?= $imovel['status'] === 'disponivel' ? 'bg-green-600' : 'bg-yellow-600' ?>"><?= ucfirst($imovel['status']) ?></span>
                        </div>
                    </div>

                    <button class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg mt-6 hover:bg-blue-700 transition-transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
                        <i class="bi bi-whatsapp mr-2"></i>
                        Entrar em Contato
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- === NOVOS ARQUIVOS JS PARA O CARROSSEL E LIGHTBOX === -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

<!-- SCRIPT ATUALIZADO -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // === INICIALIZAÇÃO DO CARROSSEL SWIPER ===
    const swiper = new Swiper('.swiper', {
        // Quantos slides mostrar por padrão
        slidesPerView: 1.5,
        spaceBetween: 15,
        // Centraliza o slide ativo
        centeredSlides: true,
        // Loop infinito
        loop: true,
        // Paginação (as bolinhas abaixo)
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        // Botões de navegação (as setas)
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        // Adaptação para telas maiores
        breakpoints: {
            // A partir de 768px de largura
            768: {
                slidesPerView: 2.5,
                spaceBetween: 20
            },
        }
    });

    // === INICIALIZAÇÃO DO FANCYBOX (LIGHTBOX) ===
    // Ele se ativa automaticamente nos links com o atributo 'data-fancybox'
    Fancybox.bind("[data-fancybox]", {
      // Suas opções customizadas, se necessário
    });


    // --- SEU CÓDIGO EXISTENTE PARA FEEDBACKS (Inalterado) ---
    const imovelId = <?= json_encode($imovel['id_imovel']) ?>;
    const visitasListContainer = document.getElementById('visitas-list');

    function openFeedbackModal(eventId, currentFeedback) {
        Swal.fire({
            title: 'Editar Feedback da Visita',
            input: 'textarea',
            inputValue: currentFeedback,
            inputLabel: 'Feedback do Cliente:',
            inputPlaceholder: 'Digite o feedback aqui...',
            showCancelButton: true,
            confirmButtonText: 'Salvar Feedback',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3b82f6',
            showLoaderOnConfirm: true,
            preConfirm: (feedback) => {
                const formData = new FormData();
                formData.append('action', 'atualizar_feedback');
                formData.append('id_evento', eventId);
                formData.append('feedback', feedback);

                return fetch('../../controllers/VisitasController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response.statusText);
                    }
                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(`A requisição falhou: ${error}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: 'Feedback atualizado com sucesso.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    loadVisitas();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: result.value.message || 'Ocorreu um erro ao salvar o feedback.',
                    });
                }
            }
        });
    }
    
    function loadVisitas() {
        if (!imovelId || !visitasListContainer) return;

        visitasListContainer.innerHTML = `<div class="text-center p-4 text-gray-500"><i class="bi bi-arrow-clockwise animate-spin text-2xl"></i><p>Carregando histórico...</p></div>`;

        fetch(`../../controllers/VisitasController.php?action=buscar_por_imovel&id_imovel=${imovelId}`)
            .then(response => response.json())
            .then(data => {
                visitasListContainer.innerHTML = '';
                if (data.success && data.visitas.length > 0) {
                    data.visitas.forEach(visita => {
                        const feedbackHtml = visita.feedback
                            ? `<p class="text-sm text-gray-600 mt-2 pl-5 border-l-2 border-gray-200"><strong>Feedback:</strong> ${visita.feedback}</p>`
                            : '<p class="text-sm text-gray-500 mt-2 pl-5">Nenhum feedback registrado.</p>';

                        const card = `
                            <div class="bg-gray-50 p-4 rounded-lg border">
                                <div class="flex flex-wrap justify-between items-center gap-2">
                                    <div>
                                        <p class="font-semibold text-gray-800">Cliente: ${visita.nome_cliente}</p>
                                        <p class="text-sm text-gray-500">Visitou em: ${new Date(visita.data_fim).toLocaleString('pt-BR')}</p>
                                    </div>
                                    <button data-event-id="${visita.id_evento}" data-feedback="${visita.feedback || ''}" class="edit-feedback-btn bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-2 rounded-lg hover:bg-blue-200">
                                        <i class="bi bi-pencil-square mr-1"></i>
                                        ${visita.feedback ? 'Editar' : 'Adicionar'} Feedback
                                    </button>
                                </div>
                                ${feedbackHtml}
                            </div>`;
                        visitasListContainer.innerHTML += card;
                    });
                } else {
                    visitasListContainer.innerHTML = '<div class="text-center p-4 text-gray-500"><p>Nenhuma visita anterior encontrada.</p></div>';
                }
            })
            .catch(error => {
                console.error('Erro ao buscar visitas:', error);
                visitasListContainer.innerHTML = '<div class="text-center p-4 text-red-500"><p>Erro ao carregar histórico.</p></div>';
            });
    }

    visitasListContainer.addEventListener('click', function(e) {
        const button = e.target.closest('.edit-feedback-btn');
        if (button) {
            const eventId = button.dataset.eventId;
            const feedback = button.dataset.feedback;
            openFeedbackModal(eventId, feedback);
        }
    });

    loadVisitas();
});
</script>
