<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "verificar-autenticacao.php";

// Debug para verificar o conteúdo da sessão
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar se o usuário está logado e tem email
if (!isset($_SESSION['logado']) || !isset($_SESSION['email'])) {
    $_SESSION['msg'] = "Por favor, faça login novamente.";
    header("Location: tela-login.php");
    exit;
}

// Função para carregar dados do usuário do JSON
function carregarDadosUsuario($email) {
    // Debug
    error_log("Tentando carregar dados para o email: " . ($email ?? 'null'));
    
    // Verificação mais rigorosa do email
    if (!isset($email) || !is_string($email) || trim($email) === '') {
        error_log("Email inválido ou vazio");
        return null;
    }

    $arquivo_json = 'dados/usuarios.json';
    if (file_exists($arquivo_json)) {
        $conteudo = file_get_contents($arquivo_json);
        if ($conteudo === false) {
            error_log("Erro ao ler o arquivo JSON");
            return null;
        }
        
        $usuarios = json_decode($conteudo, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Erro ao decodificar JSON: " . json_last_error_msg());
            return null;
        }
        
        foreach ($usuarios as $id => $usuario) {
            if (isset($usuario['email']) && $usuario['email'] === $email) {
                return ['id' => $id, 'dados' => $usuario];
            }
        }
        error_log("Usuário não encontrado no JSON");
    } else {
        error_log("Arquivo JSON não existe");
    }
    return null;
}

// Função para salvar dados do usuário em JSON
function salvarDadosUsuario($nome, $foto_perfil) {
    // Verificar se existe um email na sessão
    $email = $_SESSION['email'] ?? '';
    if (empty($email)) {
        return false;
    }

    $arquivo_json = 'dados/usuarios.json';
    $diretorio = dirname($arquivo_json);
    
    // Criar diretório se não existir
    if (!file_exists($diretorio)) {
        mkdir($diretorio, 0777, true);
    }
    
    // Carregar dados existentes ou criar array vazio
    if (file_exists($arquivo_json)) {
        $usuarios = json_decode(file_get_contents($arquivo_json), true);
    } else {
        $usuarios = [];
    }
    
    // Usar email como identificador único
    $dados_usuario = carregarDadosUsuario($email);
    $id_usuario = $dados_usuario ? $dados_usuario['id'] : uniqid();
    
    // Atualizar ou adicionar dados do usuário
    $usuarios[$id_usuario] = [
        'nome' => $nome,
        'foto_perfil' => $foto_perfil,
        'email' => $email,
        'ultima_atualizacao' => date('Y-m-d H:i:s')
    ];
    
    // Salvar no arquivo JSON
    return file_put_contents($arquivo_json, json_encode($usuarios, JSON_PRETTY_PRINT));
}

// Carregar dados do usuário ao entrar na página
$email = $_SESSION['email'] ?? '';
if (empty($email)) {
    $_SESSION['msg'] = "Sessão expirada. Por favor, faça login novamente.";
    header("Location: tela-login.php");
    exit;
}

$dados_usuario = carregarDadosUsuario($email);
if ($dados_usuario && !isset($_SESSION['nome'])) {
    $_SESSION['nome'] = $dados_usuario['dados']['nome'];
    $_SESSION['foto_perfil'] = $dados_usuario['dados']['foto_perfil'];
}

// Processar o formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_atual = $_SESSION['nome'] ?? '';
    $foto_atual = $_SESSION['foto_perfil'] ?? '';
    
    // Processar atualização do nome
    if (isset($_POST['nome']) && !empty($_POST['nome'])) {
        $nome_atual = $_POST['nome'];
        $_SESSION['nome'] = $nome_atual;
    }

    // Processar upload da foto
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
        $foto = $_FILES['foto_perfil'];
        
        // Verificar tipo de arquivo
        $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($foto['type'], $tipos_permitidos)) {
            $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
            $novo_nome = uniqid() . '.' . $ext;
            $diretorio = "uploads/perfil/";
            
            // Criar diretório se não existir
            if (!file_exists($diretorio)) {
                mkdir($diretorio, 0777, true);
            }
            
            // Upload da foto
            if (move_uploaded_file($foto['tmp_name'], $diretorio . $novo_nome)) {
                // Atualizar nome da foto na sessão
                $foto_atual = $novo_nome;
                $_SESSION['foto_perfil'] = $foto_atual;
            } else {
                $erro = "Erro ao fazer upload da foto.";
            }
        } else {
            $erro = "Tipo de arquivo não permitido. Use apenas JPG, PNG ou GIF.";
        }
    }
    
    // Salvar dados no JSON
    if (!isset($erro)) {
        if (salvarDadosUsuario($nome_atual, $foto_atual)) {
            header("Location: index.php");
            exit;
        } else {
            $erro = "Erro ao salvar os dados do usuário.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Foto de Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <div class="main-card">
            <div class="card-header">
                <h2><i class="fas fa-user-edit"></i> Editar Perfil</h2>
            </div>
            <div class="card-body">
                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger">
                        <?php echo $erro; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <!-- Campo para alterar nome -->
                    <div class="form-group mb-4">
                        <label for="nome" class="form-label">
                            <i class="fas fa-user"></i> Nome
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="nome" 
                            name="nome" 
                            value="<?php echo $_SESSION['nome'] ?? ''; ?>"
                            required
                        >
                    </div>

                    <!-- Foto de perfil existente -->
                    <div class="text-center mb-4">
                        <div class="profile-photo-container">
                            <?php if (!empty($_SESSION['foto_perfil'])): ?>
                                <img src="uploads/perfil/<?php echo $_SESSION['foto_perfil']; ?>" class="profile-photo" alt="Foto de Perfil" id="preview-foto">
                            <?php else: ?>
                                <i class="fas fa-user-circle fa-5x" id="default-icon"></i>
                            <?php endif; ?>
                        </div>
                        <div class="mt-3">
                            <label for="foto_perfil" class="btn btn-outline-primary">
                                <i class="fas fa-camera"></i> Escolher Nova Foto
                            </label>
                            <input type="file" id="foto_perfil" name="foto_perfil" class="d-none" accept="image/*" onchange="previewImagem(this);">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function previewImagem(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                var preview = document.getElementById('preview-foto');
                var defaultIcon = document.getElementById('default-icon');
                
                if (!preview) {
                    preview = document.createElement('img');
                    preview.id = 'preview-foto';
                    preview.className = 'profile-photo';
                    defaultIcon.style.display = 'none';
                    defaultIcon.parentNode.appendChild(preview);
                }
                
                preview.src = e.target.result;
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html> 