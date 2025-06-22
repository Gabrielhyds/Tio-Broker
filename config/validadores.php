<?php

function validarCpf($cpf)
{
    $cpf = preg_replace('/\D/', '', $cpf);

    if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += $cpf[$i] * (($t + 1) - $i);
        }
        $digito = ($soma * 10) % 11;
        if ($digito == 10) $digito = 0;
        if ($cpf[$t] != $digito) {
            return false;
        }
    }

    return true;
}
