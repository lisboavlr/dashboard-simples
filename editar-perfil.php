<?php
include "verificar-autenticacao.php";

// Processar o formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Processar atualização do nome
    if (isset($_POST['nome']) && !empty($_POST['nome'])) {
        $_SESSION['nome'] = $_POST['nome'];
        // Aqui você deve adicionar o código para atualizar no banco de dados
        // Exemplo: $sql = "UPDATE usuarios SET nome = ? WHERE id = ?";
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
                $_SESSION['foto_perfil'] = $novo_nome;
                
                // Aqui você deve adicionar o código para atualizar no banco de dados
                // Exemplo: $sql = "UPDATE usuarios SET foto_perfil = ? WHERE id = ?";
                
                header("Location: index.php");
                exit;
            } else {
                $erro = "Erro ao fazer upload da foto.";
            }
        } else {
            $erro = "Tipo de arquivo não permitido. Use apenas JPG, PNG ou GIF.";
        }
    }
    
    // Se chegou aqui sem foto, apenas redireciona
    if (!isset($erro)) {
        header("Location: index.php");
        exit;
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