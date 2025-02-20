<?php
session_start();

$tempo_limite = 10; // 10
$tempo_atual = time();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: tela-login");
    exit;
}

if (isset($_SESSION['ultimo_acesso'])) {
    $tempo_inativo = $tempo_atual - $_SESSION['ultimo_acesso'];
    
    if ($tempo_inativo >= $tempo_limite) {
        session_destroy();
        header("Location: tela-login?sessao_expirada=true");
        exit;
    }
}

$_SESSION['ultimo_acesso'] = $tempo_atual;
?>