<?php
// Este código deve estar no início do seu ficheiro `listar_imobiliaria.php`
// As variáveis $lista, $filtro, $total_paginas, $pagina_atual já devem ter sido definidas pelo seu controller.

/**
 * Função para formatar um número de CPF ou CNPJ com a máscara correta.
 * @param string $documento O número do documento (apenas dígitos).
 * @param string $tipo 'F' para CPF, 'J' para CNPJ.
 * @return string O documento formatado ou o original se inválido.
 */
function formatarDocumentoParaExibicao($documento, $tipo) {
    $docLimpo = preg_replace('/[^0-9]/', '', $documento);

    if ($tipo === 'F') {
        if (strlen($docLimpo) === 11) {
            // Formata para XXX.XXX.XXX-XX
            return substr($docLimpo, 0, 3) . '.' . substr($docLimpo, 3, 3) . '.' . substr($docLimpo, 6, 3) . '-' . substr($docLimpo, 9, 2);
        }
    } elseif ($tipo === 'J') {
        if (strlen($docLimpo) === 14) {
            // Formata para XX.XXX.XXX/XXXX-XX
            return substr($docLimpo, 0, 2) . '.' . substr($docLimpo, 2, 3) . '.' . substr($docLimpo, 5, 3) . '/' . substr($docLimpo, 8, 4) . '-' . substr($docLimpo, 12, 2);
        }
    }
    return $documento; // Retorna o original se não corresponder ao padrão
}
?>

<!-- Link para a biblioteca de ícones Font Awesome e Bootstrap Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
        color: #16a34a; /* green-600 */
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
        background-color: #16a34a; /* bg-green-600 */
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    }
    .crm-btn-primary:hover {
        background-color: #15803d; /* hover:bg-green-700 */
    }
    @media (min-width: 1024px) { /* lg */
        .crm-header {
            flex-direction: row;
            align-items: center;
        }
    }
</style>

<!-- Card Principal de Conteúdo -->
<div class="crm-card">
    <!-- ✅ CABEÇALHO REFATORADO COM FILTRO INTEGRADO -->
    <div class="crm-header">
        <h2 class="crm-title">
            <i class="bi bi-buildings-fill"></i>
            <span>Imobiliárias Cadastradas</span>
        </h2>
        
        <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-4 w-full lg:w-auto">
            <!-- Formulário de busca/filtro -->
            <form method="GET" action="listar_imobiliaria.php" class="flex items-center gap-2">
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="filtro" class="w-full md:w-64 border border-gray-300 rounded-md pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Buscar por nome ou documento..." value="<?= htmlspecialchars($filtro ?? '') ?>">
                </div>
                <?php if (!empty($filtro)): ?>
                    <a href="listar_imobiliaria.php" class="text-gray-500 hover:text-red-600 p-2" title="Limpar filtro">
                        <i class="bi bi-x-circle-fill text-xl"></i>
                    </a>
                <?php endif; ?>
            </form>

            <!-- Botão Nova Imobiliária -->
            <a href="cadastrar_imobiliaria.php" class="crm-btn crm-btn-primary">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Nova Imobiliária</span>
            </a>
        </div>
    </div>

    <!-- Contêiner da tabela -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white text-sm text-left text-gray-700">
            <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-600">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Nome</th>
                    <th class="px-6 py-3">Documento</th> <!-- <-- TÍTULO CORRIGIDO -->
                    <th class="px-6 py-3">Usuários</th>
                    <th class="px-6 py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (!empty($lista)): ?>
                    <?php foreach ($lista as $item): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4"><?= $item['id_imobiliaria'] ?></td>
                            <td class="px-6 py-4 font-medium text-gray-900"><?= htmlspecialchars($item['nome']) ?></td>
                            <!-- CÉLULA COM LÓGICA DE FORMATAÇÃO -->
                            <td class="px-6 py-4">
                                <?php
                                    // Determina qual documento exibir e formata-o
                                    $documento = !empty($item['cpf']) ? $item['cpf'] : $item['cnpj'];
                                    $tipo = !empty($item['cpf']) ? 'F' : 'J';
                                    echo htmlspecialchars(formatarDocumentoParaExibicao($documento, $tipo));
                                ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                    <?= $item['total_usuarios'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-4">
                                    <a href="editar_imobiliaria.php?id=<?= $item['id_imobiliaria'] ?>" class="text-blue-600 hover:text-blue-800" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="../../controllers/ImobiliariaController.php?excluir=<?= $item['id_imobiliaria'] ?>" class="text-red-500 hover:text-red-700 btn-excluir" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-400">Nenhuma imobiliária encontrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Seção de paginação -->
    <?php if (($total_paginas ?? 1) > 1): ?>
        <div class="p-4 flex justify-center border-t">
            <nav class="inline-flex -space-x-px text-sm">
                <a href="?pagina=<?= $pagina_atual - 1 ?>&filtro=<?= urlencode($filtro) ?>" class="px-3 py-1 border border-gray-300 rounded-l <?= $pagina_atual <= 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50' ?>">Anterior</a>
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <a href="?pagina=<?= $i ?>&filtro=<?= urlencode($filtro) ?>" class="px-3 py-1 border border-gray-300 <?= $i == $pagina_atual ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <a href="?pagina=<?= $pagina_atual + 1 ?>&filtro=<?= urlencode($filtro) ?>" class="px-3 py-1 border border-gray-300 rounded-r <?= $pagina_atual >= $total_paginas ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50' ?>">Próxima</a>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- Inclusão da biblioteca SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Script para o modal de confirmação de exclusão -->
<script>
document.addEventListener('DOMContentLoaded', function() {
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
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
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
