<?php
/**
 * /views/layout/_alertas.php
 *
 * Este ficheiro centraliza a exibição de mensagens de sucesso e erro usando SweetAlert2.
 * Ele deve ser incluído no seu template principal (template_base.php).
 *
 * NOTA: A biblioteca SweetAlert2 (<script src...>) deve ser incluída UMA VEZ
 * no seu template principal, de preferência no <head> ou antes do </body>.
 */

// Garante que a sessão foi iniciada antes de tentar ler as variáveis.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- O script da biblioteca SweetAlert2 deve ser incluído no seu template principal (template_base.php) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Adiciona um listener para garantir que o DOM está totalmente carregado antes de executar o script.
document.addEventListener('DOMContentLoaded', function() {

    <?php if (isset($_SESSION['sucesso'])) : ?>
        // Se houver uma mensagem de sucesso na sessão, exibe um alerta "toast"
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '<?= addslashes(htmlspecialchars($_SESSION['sucesso'])); ?>',
            showConfirmButton: false,
            timer: 3500, // O alerta desaparecerá após 3.5 segundos
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        <?php unset($_SESSION['sucesso']); // Limpa a mensagem da sessão após exibi-la ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['erro'])) : ?>
        // Se houver uma mensagem de erro na sessão, exibe um modal de erro
        Swal.fire({
            icon: 'error',
            title: 'Ocorreu um Erro',
            text: '<?= addslashes(htmlspecialchars($_SESSION['erro'])); ?>',
            confirmButtonColor: '#d33'
        });
        <?php unset($_SESSION['erro']); // Limpa a mensagem da sessão após exibi-la ?>
    <?php endif; ?>

});
</script>
