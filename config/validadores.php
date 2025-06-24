<?php

// Função para validar CPF
function validarCpf($cpf)
{
    // Remove todos os caracteres que não são dígitos (mantém apenas números)
    $cpf = preg_replace('/\D/', '', $cpf);

    // Verifica se o CPF tem exatamente 11 dígitos ou se todos os dígitos são iguais (ex: 11111111111)
    if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
        return false; // CPF inválido
    }

    // Laço para validar os dois dígitos verificadores (posição 10 e 11)
    for ($t = 9; $t < 11; $t++) {
        $soma = 0;

        // Calcula a soma dos dígitos multiplicados por pesos decrescentes
        for ($i = 0; $i < $t; $i++) {
            $soma += $cpf[$i] * (($t + 1) - $i);
        }

        // Calcula o dígito verificador
        $digito = ($soma * 10) % 11;

        // Se o resultado for 10, considera como 0
        if ($digito == 10) $digito = 0;

        // Compara o dígito calculado com o dígito real do CPF
        if ($cpf[$t] != $digito) {
            return false; // Dígito inválido
        }
    }

    // Se passou por todas as verificações, o CPF é válido
    return true;
}
