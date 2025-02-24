<?php
session_start();

// Adiciona log para debug
error_log('Requisição recebida para excluir produto');

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

function encontrarProdutoPorId($produtos, $id) {
    foreach ($produtos as $key => $produto) {
        if ($produto['id'] == $id) {
            return ['produto' => $produto, 'index' => $key];
        }
    }
    return null;
}

if (isset($_GET['id'])) {
    $produtos = carregarProdutos();
    $resultado = encontrarProdutoPorId($produtos, $_GET['id']);
    
    if ($resultado) {
        array_splice($produtos, $resultado['index'], 1);
        salvarProdutos($produtos);
        $_SESSION['sucesso'] = "Produto excluído com sucesso!";
    } else {
        $_SESSION['erro'] = "Produto não encontrado!";
    }
} else {
    error_log('GET id não definido');
}

// Redireciona de volta para a página principal
header('Location: index.php');
exit; 