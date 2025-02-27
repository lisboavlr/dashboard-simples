<?php
// POST e GET sempre vem como ARRAY

//iniciar sessao global
session_start(); //permite trabalhar com variaveis globais

//EXIBINDO DADOS DO ARRAY  se usa var_dump e nn echo
// var_dump($_POST);

if($_POST){

    //ARMAZENA OS DADOS DIGITADOS NA TELA DE LOGIN, pegar o que ta escrito no name
    $email = $_POST['email'];
    $password = $_POST['password'];
    $lembrar = isset($_POST['lembrar']) ? $_POST['lembrar'] : false;

    if($email == "ADM@LULU" && $password == "123"){
        $_SESSION['logado'] = true;
        $_SESSION['ultimo_acesso'] = time();
        $_SESSION['email'] = $email;
        
        // Carregar dados existentes ou definir padrões
        $arquivo_json = 'dados/usuarios.json';
        if (file_exists($arquivo_json)) {
            $usuarios = json_decode(file_get_contents($arquivo_json), true);
            $usuario_encontrado = false;
            if ($usuarios) {
                foreach ($usuarios as $usuario) {
                    if (isset($usuario['email']) && $usuario['email'] === $email) {
                        $_SESSION['nome'] = $usuario['nome'];
                        $_SESSION['foto_perfil'] = $usuario['foto_perfil'];
                        $usuario_encontrado = true;
                        break;
                    }
                }
            }
            if (!$usuario_encontrado) {
                $_SESSION['nome'] = "Usuário";
            }
        } else {
            $_SESSION['nome'] = "Usuário";
        }

        //vai lembrar o usuario e senha por 30 dias
        if($lembrar) {
            setcookie('lembrar_email', $email, time() + (86400 * 30), "/");
            setcookie('lembrar_senha', $password, time() + (86400 * 30), "/");
        } else {
            setcookie('lembrar_email', '', time() - 3600, "/");
            setcookie('lembrar_senha', '', time() - 3600, "/");
        }

        header("Location: ./index");
    } else{
        $_SESSION['msg'] = "E-mail ou senha inválidos";
        header("Location: ./tela-login");
    }
} else{
    //REDIRECIONA PARA TELA DE LOGIN
    header('Location: ./tela-login');
}

?>