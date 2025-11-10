<?php
// Este código deve estar no início do seu ficheiro `listar.php` ou onde você define as variáveis.
$permissao = $_SESSION['usuario']['permissao'] ?? '';
$id_imobiliaria_usuario = $_SESSION['usuario']['id_imobiliaria'] ?? null;
$imobiliarias = [];
$imoveis = [];
$idSelecionada = $_GET['id_imobiliaria'] ?? null;

// Lógica para SuperAdmin
if ($permissao === 'SuperAdmin') {
    $stmt = $connection->query("SELECT id_imobiliaria, nome FROM imobiliaria WHERE is_deleted = 0 ORDER BY nome");
    $imobiliarias = $stmt->fetch_all(MYSQLI_ASSOC);

    if (!empty($idSelecionada)) {
        $imoveis = $imovelModel->buscarPorImobiliaria($idSelecionada);
    }
} else {
    // Para outras permissões, busca imóveis da imobiliária do usuário.
    if ($id_imobiliaria_usuario) {
        $imoveis = $imovelModel->buscarPorImobiliaria($id_imobiliaria_usuario);
    }
}
?>

<!-- Link para a biblioteca de ícones Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- Adicionando Tailwind CSS para os estilos -->
<script src="https://cdn.tailwindcss.com"></script>

<style>
    /* Estilos personalizados para o CRM */
    .crm-card {
        background-color: #ffffff;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.07), 0 1px 2px -1px rgb(0 0 0 / 0.07);
        border: 1px solid #e5e7eb;
    }
    .crm-header {
        padding: 1rem 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .crm-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .crm-title i {
        color: #2563eb;
    }
    .crm-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.625rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 0.5rem;
        border: 1px solid transparent;
        transition: all 0.2s ease-in-out;
    }
    .crm-btn-primary {
        color: #ffffff;
        background-color: #2563eb;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    }
    .crm-btn-primary:hover {
        background-color: #1d4ed8;
    }
    .crm-empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        background-color: #f9fafb;
        border-radius: 0.5rem;
    }
    @media (min-width: 768px) {
        .crm-header {
            flex-direction: row;
            align-items: center;
        }
    }
</style>

<!-- Card Principal de Conteúdo -->
<div class="crm-card">
    <div class="crm-header">
        <h2 class="crm-title">
            <i class="bi bi-building"></i>
            <span>Imóveis Cadastrados</span>
        </h2>
        
        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-4 w-full md:w-auto">
            <?php if ($permissao === 'SuperAdmin'): ?>
                <form action="" method="GET" id="filter-form" class="flex items-center gap-2">
                    <select name="id_imobiliaria" id="id_imobiliaria_select" class="w-full md:w-64 p-2 border border-gray-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Filtrar por imobiliária...</option>
                        <?php foreach ($imobiliarias as $imob): ?>
                            <option value="<?= $imob['id_imobiliaria'] ?>" <?= ($idSelecionada == $imob['id_imobiliaria']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($imob['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($idSelecionada)): ?>
                        <a href="listar.php" class="text-gray-500 hover:text-red-600 p-2" title="Limpar Filtro">
                            <i class="bi bi-x-circle-fill text-xl"></i>
                        </a>
                    <?php endif; ?>
                </form>
            <?php endif; ?>
            
            <?php $podeCadastrar = ($permissao !== 'SuperAdmin' || ($permissao === 'SuperAdmin' && !empty($idSelecionada))); ?>
            <?php if ($podeCadastrar): ?>
                <a href="cadastrar.php<?= !empty($idSelecionada) ? '?id_imobiliaria=' . $idSelecionada : '' ?>" class="crm-btn crm-btn-primary">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Novo Imóvel</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="p-6 md:p-8">
        <?php if (empty($imoveis)): ?>
            <div class="crm-empty-state">
                <div class="inline-block bg-blue-100 text-blue-600 rounded-full p-4 mb-4">
                    <i class="bi bi-journal-x text-5xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800">Nenhum Imóvel Encontrado</h3>
                <p class="text-gray-500 mt-2">
                    <?php if ($permissao === 'SuperAdmin' && empty($idSelecionada)): ?>
                        Por favor, selecione e filtre uma imobiliária para começar.
                    <?php else: ?>
                        Parece que ainda não há imóveis aqui. Que tal cadastrar o primeiro?
                    <?php endif; ?>
                </p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($imoveis as $imovel): ?>
                    <?php
                    $fotos = $imovelModel->buscarArquivos($imovel['id_imovel'], 'imagem');
                    $imgUrl = !empty($fotos) ? BASE_URL . ltrim($fotos[0]['caminho'], '/') : BASE_URL . 'assets/img/no-image.jpg';
                    $status = htmlspecialchars($imovel['status']);
                    $badgeClasses = 'bg-gray-100 text-gray-800';
                    switch (strtolower($status)) {
                        case 'disponivel': $badgeClasses = 'bg-green-100 text-green-800'; break;
                        case 'vendido':
                        case 'indisponivel': $badgeClasses = 'bg-red-100 text-red-800'; break;
                        case 'reservado': $badgeClasses = 'bg-yellow-100 text-yellow-800'; break;
                    }
                    $enderecoFormatado = (!empty($imovel['cidade']) && !empty($imovel['estado'])) ? $imovel['cidade'] . ', ' . $imovel['estado'] : ($imovel['endereco'] ?: 'Endereço não informado');
                    ?>
                    <div class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group border">
                        <div class="relative w-full h-52">
                            <img src="<?= htmlspecialchars($imgUrl) ?>" alt="Imagem do Imóvel: <?= htmlspecialchars($imovel['titulo']) ?>" class="w-full h-full object-cover">
                            <span class="absolute top-3 right-3 px-3 py-1 text-sm font-semibold rounded-full <?= $badgeClasses ?>"><?= ucfirst($status) ?></span>
                        </div>
                        <div class="p-5 flex flex-col flex-grow">
                            <div class="flex-grow">
                                <p class="text-sm font-semibold text-blue-600"><?= htmlspecialchars(ucfirst($imovel['tipo'])) ?></p>
                                <h3 class="text-xl font-bold text-gray-900 mt-1 truncate" title="<?= htmlspecialchars($imovel['titulo']) ?>"><?= htmlspecialchars($imovel['titulo']) ?></h3>
                                <p class="text-2xl font-light text-gray-800 mt-2">R$ <?= number_format($imovel['preco'], 2, ',', '.') ?></p>
                                <div class="flex items-center text-gray-500 mt-3">
                                    <i class="bi bi-geo-alt-fill mr-2 flex-shrink-0"></i>
                                    <p class="text-sm truncate" title="<?= htmlspecialchars($enderecoFormatado) ?>"><?= htmlspecialchars($enderecoFormatado) ?></p>
                                </div>
                            </div>
                            <div class="flex justify-end items-center pt-4 mt-4 border-t gap-2">
                                <a href="editar.php?id=<?= $imovel['id_imovel'] ?>" class="font-semibold text-gray-600 hover:text-blue-600 p-1" title="Editar"><i class="bi bi-pencil-square"></i></a>
                                <!-- ✅ CORREÇÃO: Removido o 'onclick' e adicionada a classe 'btn-excluir' -->
                                <a href="../../controllers/ImovelController.php?action=excluir&id=<?= $imovel['id_imovel'] ?>" class="font-semibold text-red-600 hover:text-red-800 p-1 btn-excluir" title="Excluir"><i class="bi bi-trash-fill"></i></a>
                                <a href="mostrar.php?id=<?= $imovel['id_imovel'] ?>" class="font-semibold text-white bg-gray-800 hover:bg-black rounded-md px-4 py-2 transition-colors text-sm">Ver Detalhes</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ✅ INCLUSÃO DA BIBLIOTECA SWEETALERT2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ✅ SCRIPT PARA FILTRO E MODAL DE EXCLUSÃO -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lógica para o filtro automático
    const filterSelect = document.getElementById('id_imobiliaria_select');
    if(filterSelect) {
        filterSelect.addEventListener('change', function() {
            document.getElementById('filter-form').submit();
        });
    }

    // Lógica para o modal de exclusão com SweetAlert2
    const botoesExcluir = document.querySelectorAll('.btn-excluir');
    botoesExcluir.forEach(function(botao) {
        botao.addEventListener('click', function(event) {
            event.preventDefault();
            const urlParaExcluir = this.href;
            Swal.fire({
                title: 'Você tem certeza?',
                text: "Esta ação não poderá ser revertida!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = urlParaExcluir;
                }
            });
        });
    });
});
</script>
