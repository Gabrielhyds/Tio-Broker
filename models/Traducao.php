<?php

class Traducao
{
    private $basePath = __DIR__ . '/../lang';

    public function carregar($modulo, $idioma)
    {
        $arquivo = "{$this->basePath}/{$modulo}/{$idioma}.json";

        if (!file_exists($arquivo)) {
            return null;
        }

        $conteudo = file_get_contents($arquivo);
        return json_decode($conteudo, true);
    }

    public function salvar($modulo, $idioma, $dados)
    {
        $arquivo = "{$this->basePath}/{$modulo}/{$idioma}.json";

        if (!is_dir("{$this->basePath}/{$modulo}")) {
            mkdir("{$this->basePath}/{$modulo}", 0755, true);
        }

        $json = json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($arquivo, $json) !== false;
    }
}
