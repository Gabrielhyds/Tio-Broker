<?php
@session_start();

// Se o usuário não estiver logado, envia para o login
if (!isset($_SESSION['usuario'])) {
    header('Location: ../auth/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Imobiliária</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Ícones do Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-container {
            max-width: 500px;
            margin: 2rem auto;
        }

        .card-header {
            background-color: #007bff;
            color: #fff;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>

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

<body>

    <?php
    // Incluir o dashboard de acordo com o perfil do usuário (Navbar lateral ou topo, conforme seu layout)
    if ($_SESSION['usuario']['permissao'] === 'SuperAdmin') {
        include_once '../dashboards/dashboard_superadmin.php';
    } elseif ($_SESSION['usuario']['permissao'] === 'Admin') {
        include_once '../dashboards/dashboard_admin.php';
    } elseif ($_SESSION['usuario']['permissao'] === 'Coordenador') {
        include_once '../dashboards/dashboard_coordenador.php';
    } else {
        include_once '../dashboards/dashboard_corretor.php';
    }
    ?>

    <div class="card card-container shadow-sm">
        <div class="card-header text-center">
            <h4 class="mb-0">
                <i class="bi bi-building-add"></i>
                Cadastrar Nova Imobiliária
            </h4>
        </div>
        <div class="card-body">
            <form action="../../controllers/ImobiliariaController.php" method="POST" onsubmit="validarFormulario(event)">
                <input type="hidden" name="action" value="cadastrar">

                <div class="mb-3">
                    <label for="cnpj" class="form-label">CNPJ</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                        <input
                            type="text"
                            class="form-control"
                            name="cnpj"
                            id="cnpj"
                            maxlength="18"
                            placeholder="00.000.000/0000-00"
                            required
                            onkeypress="apenasNumeros(event)"
                            oninput="formatarCNPJ(this)"
                            onblur="buscarDadosCNPJ(this)">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="nome" class="form-label">Nome da Imobiliária</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-building"></i></span>
                        <input
                            type="text"
                            class="form-control"
                            name="nome"
                            id="nome"
                            placeholder="Ex: Imobiliária Exemplo Ltda."
                            required>
                    </div>
                </div>

                <!-- Botões -->
                <div class="d-flex justify-content-between">
                    <a href="../dashboards/dashboard_superadmin.php" class="btn btn-cancel">
                        <i class="bi bi-arrow-left-circle me-1"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-submit">
                        <i class="bi bi-save me-1"></i> Cadastrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JS (Popper + Bootstrap) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>