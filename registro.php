<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">
</head>
<body>
    <button id="theme-toggle" class="theme-toggle" aria-label="Alternar tema">
        <i class="fas fa-sun light-icon"></i>
        <i class="fas fa-moon dark-icon"></i>
    </button>

    <div class="login-container register-container">
        <div class="login-header">
            <div class="login-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <h2 class="animate">Criar Conta</h2>
            <p>Preencha os dados para se registrar</p>
        </div>

        <form action="processar_registro.php" method="POST" class="login-form">
            <div class="form-group">
                <label class="form-label">Nome Completo</label>
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="nome" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">E-mail</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Senha</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="senha" class="form-control" required>
                    <i class="fas fa-eye password-toggle"></i>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Confirmar Senha</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="confirmar_senha" class="form-control" required>
                    <i class="fas fa-eye password-toggle"></i>
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn-login">
                    <i class="fas fa-user-plus"></i>
                    Criar Conta
                </button>
                
                <div class="register-link">
                    <p>Já tem uma conta?</p>
                    <a href="tela-login.php" class="btn-register">
                        <i class="fas fa-sign-in-alt"></i>
                        Fazer Login
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script src="js/theme.js"></script>
</body>
</html> 