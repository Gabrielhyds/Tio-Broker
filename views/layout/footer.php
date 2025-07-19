<?php
/*
|--------------------------------------------------------------------------
| ARQUIVO: footer.php (VERSÃO FINAL CORRIGIDA)
|--------------------------------------------------------------------------
| Adicionada a classe 'translating' para evitar o "pisca-pisca" do idioma.
*/
?>
<!-- A tag <footer> define o rodapé de uma seção ou de uma página. -->
<footer class="text-center p-4 text-sm text-gray-500 bg-white border-t border-gray-200">
    <!-- 
        &copy; é o código HTML para o símbolo de copyright (©).
        O código PHP `<?php echo date('Y'); ?>` exibe dinamicamente o ano atual.
        Isso garante que o ano do copyright esteja sempre atualizado.
    -->
    &copy; <?php echo date('Y'); ?> <span class="translating" data-i18n="footer.copyright">Tio Broker. Todos os direitos reservados.</span>
</footer>