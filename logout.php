<?php
session_start();

// Destruir todas as variáveis de sessão
$_SESSION = array();

// Destruir a sessão
session_destroy();

// Destruir o cookie da sessão se existir
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Redirecionar para a página de login
header('Location: tela-login.php');
exit(); 