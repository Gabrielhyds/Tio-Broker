<?php
/**
 * /views/layout/_alertas.php
 * * Este ficheiro centraliza a exibição de mensagens de sucesso e erro.
 * Ele deve ser incluído no seu template principal (template_base.php)
 * para que as mensagens apareçam em todas as páginas do sistema.
 */

// Garante que a sessão foi iniciada antes de tentar ler as variáveis.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php if (isset($_SESSION['erro'])) : ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
        <p class="font-bold">Erro!</p>
        <p><?= htmlspecialchars($_SESSION['erro']); ?></p>
        <?php unset($_SESSION['erro']); // Limpa a mensagem após exibi-la ?>
    </div>
<?php endif; ?>
    
<?php if (isset($_SESSION['sucesso'])) : ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6" role="alert">
        <p class="font-bold">Sucesso!</p>
        <p><?= htmlspecialchars($_SESSION['sucesso']); ?></p>
        <?php unset($_SESSION['sucesso']); // Limpa a mensagem após exibi-la ?>
    </div>
<?php endif; ?>