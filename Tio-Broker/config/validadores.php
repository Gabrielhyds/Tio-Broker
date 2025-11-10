<?php

/**
 * Função para validar o CPF.
 *
 * @param string $cpf O CPF a ser validado, podendo conter máscara.
 * @return bool Retorna true se o CPF for válido, false caso contrário.
 */
function validarCpf($cpf)
{
    // 1. Remove qualquer caractere que não seja número
    $cpf = preg_replace('/[^0-9]/is', '', $cpf);

    // 2. Verifica se a string tem 11 caracteres
    if (strlen($cpf) != 11) {
        return false;
    }

    // 3. Verifica se todos os dígitos são iguais (ex: 111.111.111-11), o que é inválido
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // 4. Calcula os dígitos verificadores para validar o CPF
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }

    // Se passou por todas as verificações, o CPF é válido
    return true;
}


/**
 * Função para validar um número de telefone.
 * Verifica se, após remover a formatação, o número contém 10 ou 11 dígitos.
 *
 * @param string $telefone O número de telefone a ser validado.
 * @return bool Retorna true se o telefone for válido, false caso contrário.
 */
function validarTelefone($telefone)
{
    // Remove todos os caracteres que não são dígitos
    $telefone = preg_replace('/\D/', '', $telefone);

    // Verifica se o número de telefone tem 10 (fixo) ou 11 (celular) dígitos
    $tamanho = strlen($telefone);
    if ($tamanho >= 10 && $tamanho <= 11) {
        return true; // Telefone válido
    }

    return false; // Telefone inválido
}

