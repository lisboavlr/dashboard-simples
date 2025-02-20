<?php
session_start(); //precisa ter em todas as paginas que vai usar sessao

// verifica o cookie
if(isset($_COOKIE['lembrar_email']) && isset($_COOKIE['lembrar_senha'])) {
    $email_salvo = $_COOKIE['lembrar_email'];
    $senha_salva = $_COOKIE['lembrar_senha'];
} else {
    $email_salvo = '';
    $senha_salva = '';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">
</head>
<body>
    <button id="theme-toggle" class="theme-toggle" aria-label="Alternar tema">
        <i class="fas fa-sun light-icon"></i>
        <i class="fas fa-moon dark-icon"></i>
    </button>

    <!-- Adicione este HTML logo após a abertura do body -->
    <div class="loading-overlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-message">
                <i class="loading-icon"></i>
                <div class="loading-text" id="loadingText">Verificando senha...</div>
            </div>
        </div>
    </div>

    <?php 
    if(isset($_SESSION['msg'])){
        echo '<div class="alert alert-danger" role="alert">'.$_SESSION['msg'].'</div>';
        unset($_SESSION['msg']);
    }
    
    if(isset($_GET['sessao_expirada'])){
        echo '<div class="alert alert-warning" role="alert">
                Sua sessão expirou por inatividade. Por favor, faça login novamente.
              </div>';
    }
    ?>

    <div class="login-container">
        <div class="login-header">
            <div class="login-icon">
                <i class="fas fa-user-circle"></i>
            </div>
            <h2 class="animate">Bem-vindo</h2>
            <p>Faça login para acessar o sistema</p>
        </div>
        <form id="loginForm" action="validar-login" method="post">
            <div class="form-group mb-4">
                <label for="email" class="form-label">E-mail</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="nome@exemplo.com" required 
                           value="<?php echo $email_salvo; ?>">
                </div>
            </div>
            <div class="form-group mb-4">
                <label for="password" class="form-label">Senha</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Senha" required 
                           value="<?php echo $senha_salva; ?>">
                    <span class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </span>
                </div>
            </div>
            <div class="form-check mb-4">
                <input type="checkbox" class="form-check-input" id="lembrar" name="lembrar" 
                       <?php echo ($email_salvo != '') ? 'checked' : ''; ?>>
                <label class="form-check-label" for="lembrar">Lembrar meus dados</label>
            </div>
            <div class="form-footer">
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    Entrar
                </button>
                
                <div class="register-link">
                    <p>Não tem uma conta?</p>
                    <a href="registro.php" class="btn-register">
                        <i class="fas fa-user-plus"></i>
                        Registrar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script>
        // esconder alertas após 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });

        // função para mostrar e ocultar a senha (code antigo)
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const loginContainer = document.querySelector('.login-container');
            const loadingOverlay = document.querySelector('.loading-overlay');
            const loadingText = document.getElementById('loadingText');
            const loadingIcon = document.querySelector('.loading-icon');
            
            // Array com as mensagens, ícones e seus tempos
            const messages = [
                { 
                    text: "Verificando senha...", 
                    icon: "fas fa-key",
                    time: 0 
                },
                { 
                    text: "Verificando segurança...", 
                    icon: "fas fa-shield-alt",
                    time: 1200 
                },
                { 
                    text: "Entrando...", 
                    icon: "fas fa-door-open",
                    time: 2400 
                }
            ];

            // Função para animar a troca de mensagens e ícones
            function animateMessage(message, icon) {
                loadingText.style.opacity = '0';
                loadingText.style.transform = 'translateX(20px)';
                loadingIcon.style.opacity = '0';
                loadingIcon.style.transform = 'scale(0.5)';
                
                setTimeout(() => {
                    loadingText.textContent = message;
                    loadingIcon.className = `loading-icon ${icon}`;
                    
                    loadingText.style.opacity = '1';
                    loadingText.style.transform = 'translateX(0)';
                    loadingIcon.style.opacity = '1';
                    loadingIcon.style.transform = 'scale(1)';
                    
                    // Adiciona animação de bounce ao ícone
                    loadingIcon.style.animation = 'bounce 0.5s ease';
                    setTimeout(() => {
                        loadingIcon.style.animation = '';
                    }, 500);
                }, 300);
            }

            // Inicia a sequência de animação
            loginContainer.classList.add('fade-out');
            
            setTimeout(() => {
                loadingOverlay.style.display = 'flex';
                setTimeout(() => {
                    loadingOverlay.style.opacity = '1';
                    
                    // Inicia a sequência de mensagens
                    messages.forEach(msg => {
                        setTimeout(() => {
                            animateMessage(msg.text, msg.icon);
                        }, msg.time);
                    });

                    // Envia o formulário após todas as animações
                    setTimeout(() => {
                        form.submit();
                    }, 3600);
                }, 50);
            }, 300);
        });
    </script>

    <script src="js/theme.js"></script>
</body>
</html>