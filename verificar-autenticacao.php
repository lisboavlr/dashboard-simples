<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Função para carregar dados do usuário do JSON
function carregarDadosUsuarioJSON($email) {
    $arquivo_json = 'dados/usuarios.json';
    if (file_exists($arquivo_json)) {
        $usuarios = json_decode(file_get_contents($arquivo_json), true);
        if ($usuarios) {
            foreach ($usuarios as $usuario) {
                if (isset($usuario['email']) && $usuario['email'] === $email) {
                    return $usuario;
                }
            }
        }
    }
    return null;
}

// Verificar autenticação e carregar dados do usuário
$tempo_limite = 60; // 60 segundos
$tempo_atual = time();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: tela-login");
    exit;
}

// Carregar dados do usuário se não estiverem na sessão
if (isset($_SESSION['email']) && (!isset($_SESSION['nome']) || !isset($_SESSION['foto_perfil']))) {
    $dados_usuario = carregarDadosUsuarioJSON($_SESSION['email']);
    if ($dados_usuario) {
        $_SESSION['nome'] = $dados_usuario['nome'];
        $_SESSION['foto_perfil'] = $dados_usuario['foto_perfil'];
    }
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