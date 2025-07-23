<?php
// Verifica permissão do usuário
$permissao = $_SESSION['usuario']['permissao'] ?? '';
$imobiliarias = [];
$imoveis = []; // Inicializa a variável para evitar erros
$idSelecionada = null; // Inicializa a variável

// Lógica para SuperAdmin
if ($permissao === 'SuperAdmin') {
    $stmt = $connection->query("SELECT id_imobiliaria, nome FROM imobiliaria WHERE is_deleted = 0 ORDER BY nome");
    $imobiliarias = $stmt->fetch_all(MYSQLI_ASSOC);
    $idSelecionada = $_GET['id_imobiliaria'] ?? null;

    // Se uma imobiliária foi selecionada, busca os imóveis
    if ($idSelecionada) {
        $imoveis = $imovelModel->buscarPorImobiliaria($idSelecionada);
    }
} else {
    // Para todas as outras permissões (Corretor, Admin, etc.),
    // busca apenas os imóveis da imobiliária associada ao usuário logado.
    if ($id_imobiliaria_usuario) {
        $imoveis = $imovelModel->buscarPorImobiliaria($id_imobiliaria_usuario);
    } else {
        // Caso de segurança: um usuário deste tipo deveria ter uma imobiliária.
        // Se não tiver, a lista de imóveis ficará vazia.
        $imoveis = [];
    }
}
?>

<!-- Link para a biblioteca de ícones Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- Adicionando Tailwind CSS para os estilos -->
<script src="https://cdn.tailwindcss.com"></script>

<style>
    /* Estilos personalizados com o prefixo 'crm-' para evitar conflitos */
    .crm-card {
        background-color: #ffffff;
        border-radius: 0.75rem;
        /* rounded-xl */
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.07), 0 1px 2px -1px rgb(0 0 0 / 0.07);
        /* shadow */
        border: 1px solid #e5e7eb;
        /* border-gray-200 */
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
        /* text-xl */
        font-weight: 600;
        /* font-semibold */
        color: #1f2937;
        /* text-gray-800 */
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .crm-title i {
        color: #2563eb;
        /* text-blue-600 */
    }

    .crm-actions {
        display: flex;
        gap: 0.75rem;
        width: 100%;
    }

    .crm-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.625rem 1rem;
        font-size: 0.875rem;
        /* text-sm */
        font-weight: 600;
        /* font-semibold */
        border-radius: 0.5rem;
        /* rounded-lg */
        border: 1px solid transparent;
        transition: all 0.2s ease-in-out;
    }

    .crm-btn-primary {
        color: #ffffff;
        background-color: #2563eb;
        /* bg-blue-600 */
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    }

    .crm-btn-primary:hover {
        background-color: #1d4ed8;
        /* hover:bg-blue-700 */
    }

    .crm-empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        background-color: #f9fafb;
        /* bg-gray-50 */
        border-radius: 0.5rem;
    }

    @media (min-width: 768px) {

        /* md */
        .crm-header {
            flex-direction: row;
            align-items: center;
        }

        .crm-actions {
            width: auto;
        }
    }
</style>

<!-- Seção de Seleção de Imobiliária (só aparece para SuperAdmin) -->
<?php if ($permissao === 'SuperAdmin'): ?>
    <div class="crm-card mb-8">
        <div class="p-5">
            <form action="" method="GET" class="flex flex-wrap items-end gap-4 md:flex-nowrap">
                <div class="flex-grow w-full md:w-auto">
                    <label for="id_imobiliaria_select" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Imobiliária</label>
                    <select name="id_imobiliaria" id="id_imobiliaria_select" class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">-- Ver todos os imóveis --</option>
                        <?php foreach ($imobiliarias as $imob): ?>
                            <option value="<?= $imob['id_imobiliaria'] ?>" <?= (isset($idSelecionada) && $idSelecionada == $imob['id_imobiliaria']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($imob['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex-shrink-0">
                    <button type="submit" class="crm-btn crm-btn-primary w-full md:w-auto">
                        <i class="bi bi-search"></i>
                        <span>Filtrar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<!-- Card Principal de Conteúdo -->
<div class="crm-card">
    <div class="crm-header">
        <h2 class="crm-title">
            <i class="bi bi-building"></i>
            <span>Imóveis Cadastrados</span>
        </h2>
        <?php
        $podeCadastrar = ($permissao !== 'SuperAdmin' || ($permissao === 'SuperAdmin' && isset($idSelecionada)));
        ?>
        <?php if ($podeCadastrar): ?>
            <div class="crm-actions">
                <a href="cadastrar.php<?= (isset($idSelecionada) && $idSelecionada) ? '?id_imobiliaria=' . $idSelecionada : '' ?>" class="crm-btn crm-btn-primary">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Novo Imóvel</span>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Conteúdo Principal (Grid de Imóveis ou Mensagem de Vazio) -->
    <div class="p-6 md:p-8">
        <?php if (empty($imoveis)): ?>
            <div class="crm-empty-state">
                <div class="inline-block bg-blue-100 text-blue-600 rounded-full p-4 mb-4">
                    <i class="bi bi-journal-x text-5xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800">Nenhum Imóvel Encontrado</h3>
                <p class="text-gray-500 mt-2">
                    <?php if ($permissao === 'SuperAdmin' && !$idSelecionada): ?>
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
                        case 'à venda':
                        case 'disponível':
                            $badgeClasses = 'bg-green-100 text-green-800';
                            break;
                        case 'alugado':
                        case 'vendido':
                            $badgeClasses = 'bg-red-100 text-red-800';
                            break;
                        case 'em negociação':
                            $badgeClasses = 'bg-yellow-100 text-yellow-800';
                            break;
                    }
                    ?>
                    <div class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group border">
                        <div class="relative w-full h-52">
                            <img src="<?= htmlspecialchars($imgUrl) ?>" alt="Imagem do Imóvel: <?= htmlspecialchars($imovel['titulo']) ?>" class="w-full h-full object-cover">
                            <span class="absolute top-3 right-3 px-3 py-1 text-sm font-semibold rounded-full <?= $badgeClasses ?>"><?= $status ?></span>
                        </div>
                        <div class="p-5 flex flex-col flex-grow">
                            <div class="flex-grow">
                                <p class="text-sm font-semibold text-blue-600"><?= htmlspecialchars($imovel['tipo']) ?></p>
                                <h3 class="text-xl font-bold text-gray-900 mt-1 truncate" title="<?= htmlspecialchars($imovel['titulo']) ?>"><?= htmlspecialchars($imovel['titulo']) ?></h3>
                                <p class="text-2xl font-light text-gray-800 mt-2">R$ <?= number_format($imovel['preco'], 2, ',', '.') ?></p>
                                <div class="flex items-center text-gray-500 mt-3">
                                    <i class="bi bi-geo-alt-fill mr-2 flex-shrink-0"></i>
                                    <p class="text-sm truncate" title="<?= htmlspecialchars($imovel['endereco']) ?>"><?= htmlspecialchars($imovel['endereco']) ?></p>
                                </div>
                            </div>
                            <div class="flex justify-end items-center pt-4 mt-4 border-t gap-2">
                                <a href="editar.php?id=<?= $imovel['id_imovel'] ?>" class="flex items-center gap-1 font-semibold text-gray-600 hover:text-blue-600 transition-colors px-2 py-1" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="../../controllers/ImovelController.php?action=excluir&id=<?= $imovel['id_imovel'] ?>"
                                    class="flex items-center gap-1 font-semibold text-red-600 hover:text-red-800 transition-colors px-2 py-1"
                                    onclick="return confirm('Tem certeza que deseja excluir este imóvel?')"><i class="bi bi-trash-fill"></i></a>
                                <a href="mostrar.php?id=<?= $imovel['id_imovel'] ?>" class="font-semibold text-white bg-gray-800 hover:bg-black rounded-md px-4 py-2 transition-colors">Ver Detalhes</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>