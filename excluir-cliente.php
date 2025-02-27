<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "verificar-autenticacao.php";

// Função para carregar clientes
function carregarClientes() {
    $arquivo = 'dados/clientes.json';
    if (!file_exists('dados')) {
        mkdir('dados', 0777, true);
    }
    if (file_exists($arquivo)) {
        $json = file_get_contents($arquivo);
        return json_decode($json, true) ?: [];
    }
    return [];
}

// Função para salvar clientes
function salvarClientes($clientes) {
    $arquivo = 'dados/clientes.json';
    if (!file_exists('dados')) {
        mkdir('dados', 0777, true);
    }
    file_put_contents($arquivo, json_encode($clientes, JSON_PRETTY_PRINT));
}

// Verifica se foi fornecido um ID
if (!isset($_GET['id'])) {
    $_SESSION['erro'] = "ID do cliente não fornecido!";
    header("Location: clientes.php");
    exit();
}

$id = intval($_GET['id']);
$clientes = carregarClientes();
$cliente_encontrado = false;

// Remove o cliente com o ID fornecido
foreach ($clientes as $key => $cliente) {
    if (isset($cliente['id']) && $cliente['id'] === $id) {
        unset($clientes[$key]);
        $cliente_encontrado = true;
        break;
    }
}

if ($cliente_encontrado) {
    // Reindexar o array após a remoção
    $clientes = array_values($clientes);
    salvarClientes($clientes);
    $_SESSION['sucesso'] = "Cliente excluído com sucesso!";
} else {
    $_SESSION['erro'] = "Cliente não encontrado!";
}

header("Location: clientes.php");
exit();
?> 