<?php
function carregarProdutos() {
    $arquivo = 'produtos.json';
    if (file_exists($arquivo)) {
        $json = file_get_contents($arquivo);
        return json_decode($json, true) ?: [];
    }
    return [];
}

function salvarProdutos($produtos) {
    $arquivo = 'produtos.json';
    file_put_contents($arquivo, json_encode($produtos, JSON_PRETTY_PRINT));
}

$produtos = carregarProdutos();
$id = 1;

foreach ($produtos as &$produto) {
    if (!isset($produto['id'])) {
        $produto['id'] = $id++;
    }
}

salvarProdutos($produtos);
echo "IDs atualizados com sucesso!"; 