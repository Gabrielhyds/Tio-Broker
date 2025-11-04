<!-- Contêiner principal do formulário, com estilo de cartão. -->
<div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow-md">
    <h1 class="text-2xl font-semibold text-gray-800 mb-2">Assinatura de Contrato</h1>
    <p class="text-sm text-gray-500 mb-6">Simulação de assinatura digital — uso interno do TioBroker</p>

    <div class="border-t border-gray-200 pt-4 mb-6 text-left">
      <p class="text-gray-700 leading-relaxed">
        Este é um contrato de teste para o sistema imobiliário <strong>TioBroker</strong>.
        O objetivo é apenas simular a assinatura digital. Nenhum valor jurídico é atribuído
        a este documento.
      </p>
    </div>

    <form id="form-assinatura" method="POST" action="assinar.php" class="space-y-4 text-left">
      <input type="hidden" name="contrato_texto" value="Este é um contrato de teste para o sistema imobiliário.">

      <div>
        <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome completo</label>
        <input type="text" id="nome" name="nome" required
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
      </div>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
        <input type="email" id="email" name="email" required
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
      </div>

      <button type="submit"
        class="w-full bg-blue-600 text-white font-semibold py-2.5 rounded-lg hover:bg-blue-700 transition">
        Assinar Contrato
      </button>
    </form>
</div>