<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Imobiliária</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function apenasNumeros(event) {
            const tecla = event.key;
            if (!/\d/.test(tecla)) {
                event.preventDefault();
            }
        }

        function formatarCNPJ(campo) {
            let cnpj = campo.value.replace(/\D/g, '');
            if (cnpj.length > 14) cnpj = cnpj.slice(0, 14);

            cnpj = cnpj.replace(/^(\d{2})(\d)/, "$1.$2");
            cnpj = cnpj.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
            cnpj = cnpj.replace(/\.(\d{3})(\d)/, ".$1/$2");
            cnpj = cnpj.replace(/(\d{4})(\d)/, "$1-$2");

            campo.value = cnpj;
        }

        function buscarDadosCNPJ(campo) {
            let cnpj = campo.value.replace(/\D/g, '');

            if (cnpj.length === 14) {
                const nomeCampo = document.getElementById('nome');
                nomeCampo.value = "Buscando...";
                nomeCampo.readOnly = true;

                fetch(`../../controllers/BuscarCNPJController.php?cnpj=${cnpj}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "OK") {
                            nomeCampo.value = data.nome;
                        } else {
                            nomeCampo.value = "";
                            alert('CNPJ inválido ou não encontrado.');
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao buscar CNPJ:', error);
                        nomeCampo.value = "";
                        alert('Erro ao buscar informações do CNPJ.');
                    })
                    .finally(() => {
                        nomeCampo.readOnly = false;
                    });
            }
        }

        function validarFormulario(event) {
            const cnpjCampo = document.getElementById('cnpj');
            const cnpjLimpo = cnpjCampo.value.replace(/\D/g, '');

            if (cnpjLimpo.length !== 14) {
                event.preventDefault();
                alert('CNPJ inválido! O CNPJ deve conter 14 números.');
                cnpjCampo.focus();
            }
        }
    </script>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Cadastrar Nova Imobiliária</h2>
        <form action="../../controllers/ImobiliariaController.php" method="POST" onsubmit="validarFormulario(event)">
            <input type="hidden" name="action" value="cadastrar">
            <div class="mb-3">
                <label for="cnpj" class="form-label">CNPJ</label>
                <input type="text" class="form-control" name="cnpj" id="cnpj" maxlength="18" required 
                       onkeypress="apenasNumeros(event)" 
                       oninput="formatarCNPJ(this)" 
                       onblur="buscarDadosCNPJ(this)">
            </div>
            <div class="mb-3">
                <label for="nome" class="form-label">Nome da Imobiliária</label>
                <input type="text" class="form-control" name="nome" id="nome" required>
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>
</body>
</html>
