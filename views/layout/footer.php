<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: footer.php (VERSÃO REVISADA)
|--------------------------------------------------------------------------
| Nenhuma alteração funcional necessária.
| A classe 'translating' previne o "pisca-pisca" da tradução.
*/
?>
<!-- 
    Footer:
    - 'flex-shrink-0' impede que o rodapé encolha.
    - Ele ficará posicionado corretamente no final do layout flex-col.
-->
<footer class="flex-shrink-0 text-center p-4 text-sm text-gray-500 bg-white border-t border-gray-200">
    &copy; <?php echo date('Y'); ?> <span class="translating" data-i18n="footer.copyright">Tio Broker. Todos os direitos reservados.</span>
</footer>