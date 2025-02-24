<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Função para carregar produtos do arquivo JSON
function carregarProdutos() {
    $arquivo = 'produtos.json';
    if (file_exists($arquivo)) {
        $json = file_get_contents($arquivo);
        return json_decode($json, true) ?: [];
    }
    return [];
}

// Função para salvar produtos no arquivo JSON
function salvarProdutos($produtos) {
    $arquivo = 'produtos.json';
    file_put_contents($arquivo, json_encode($produtos, JSON_PRETTY_PRINT));
}

// Processamento do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produtos = carregarProdutos();
    
    // Encontra o maior ID atual
    $maxId = 0;
    foreach ($produtos as $produto) {
        if (isset($produto['id']) && intval($produto['id']) > $maxId) {
            $maxId = intval($produto['id']);
        }
    }
    
    $novo_produto = array(
        'id' => $maxId + 1,
        'productName' => $_POST['productName'],
        'productPrice' => floatval($_POST['productPrice']),
        'productDescription' => $_POST['productDescription'],
        'productQuantity' => intval($_POST['productQuantity']),
        'data_cadastro' => date('Y-m-d H:i:s')
    );
    
    $produtos[] = $novo_produto;
    salvarProdutos($produtos);
    
    $_SESSION['sucesso'] = "Produto cadastrado com sucesso!";
    header("Location: index.php");
    exit();
}

// Processamento do cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_cadastro'])) {
    if (!isset($_SESSION['produtos'])) {
        $_SESSION['produtos'] = array();
    }
    
    $_SESSION['produtos'][] = array(
        'nome' => $_POST['nome'],
        'preco' => floatval($_POST['preco']),
        'descricao' => $_POST['descricao'],
        'data_cadastro' => date('Y-m-d H:i:s')
    );
    
    echo json_encode(['success' => true]);
    exit;
}

//chama o arquivo abaixo nesta tela
include "verificar-autenticacao.php";

$_SESSION['teste'] = "teste123"; // Teste da sessão

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['produtos'])) {
        $_SESSION['produtos'] = array();
    }
    
    $_SESSION['produtos'][] = array(
        'productName' => $_POST['productName'],
        'productPrice' => $_POST['productPrice'],
        'productDescription' => $_POST['productDescription'],
        'productQuantity' => $_POST['productQuantity']
    );
    
    $_SESSION['msg'] = "Produto cadastrado com sucesso!";
    header("Location: index.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Cadastro de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/dashboard.css" rel="stylesheet">
    <style>
    .alert-success {
        padding: 15px 40px 15px 20px;
        margin: 20px 0;
        border: 2px solid #28a745;
        border-radius: 15px;
        background-color: #d4edda;
        color: #155724;
        position: relative;
        animation: glowingSuccess 2s infinite;
        box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 16px;
    }

    .alert-success .close-btn {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #155724;
        font-size: 20px;
        cursor: pointer;
        padding: 5px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .alert-success .close-btn:hover {
        background-color: rgba(40, 167, 69, 0.1);
    }

    @keyframes glowingSuccess {
        0% {
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
        }
        50% {
            box-shadow: 0 0 20px rgba(40, 167, 69, 0.8);
        }
        100% {
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
        }
    }

    @keyframes slideIn {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .edit-btn {
        color: #ffc107;
        margin-right: 10px;
        transition: color 0.3s ease;
    }

    .edit-btn:hover {
        color: #ff9800;
    }

    .products-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .products-table th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        padding: 12px 15px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
    }

    .products-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #dee2e6;
        color: #212529;
    }

    .products-table tr:last-child td {
        border-bottom: none;
    }

    .products-table tr:hover {
        background-color: #f8f9fa;
    }

    .actions {
        white-space: nowrap;
        text-align: center;
    }

    .edit-btn, .delete-btn {
        padding: 6px 12px;
        text-decoration: none;
        border-radius: 4px;
        margin: 0 3px;
        transition: all 0.3s ease;
    }

    .edit-btn {
        color: #ffc107;
    }

    .delete-btn {
        color: #dc3545;
    }

    .edit-btn:hover {
        color: #e0a800;
    }

    .delete-btn:hover {
        color: #c82333;
    }

    .products-table td:nth-child(2) {
        /* Coluna de preço */
        text-align: right;
    }

    .products-table td:nth-child(4) {
        /* Coluna de quantidade */
        text-align: center;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .products-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
        
        .products-table th, 
        .products-table td {
            padding: 8px 10px;
        }
    }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sistema de Mensagens -->
        <?php if (isset($_SESSION['erro'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['erro']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['erro']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['sucesso'])): ?>
            <div class="alert-success">
                <span><?php echo $_SESSION['sucesso']; ?></span>
                <button class="close-btn" type="button">&times;</button>
            </div>
            <?php unset($_SESSION['sucesso']); ?>
        <?php endif; ?>

        <!-- Header da Dashboard -->
        <header class="dashboard-header">
            <div class="dashboard-title">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard de Produtos
            </div>
            <div class="header-actions">
                <!-- Remova ou comente este trecho -->
                <!--
                <button id="theme-toggle" class="theme-toggle" aria-label="Alternar tema">
                    <i class="fas fa-sun light-icon"></i>
                    <i class="fas fa-moon dark-icon"></i>
                </button>
                -->
                <div class="user-menu">
                    <span class="user-info">
                        <?php if (!empty($_SESSION['foto_perfil'])): ?>
                            <img src="uploads/perfil/<?php echo $_SESSION['foto_perfil']; ?>" class="profile-pic" alt="Foto de Perfil">
                        <?php else: ?>
                            <i class="fas fa-user-circle"></i>
                        <?php endif; ?>
                        <?php echo $_SESSION['nome'] ?? 'Usuário'; ?>
                    </span>
                    <div class="user-dropdown">
                        <a href="editar-perfil.php" class="dropdown-item">
                            <i class="fas fa-user-edit"></i>
                        </a>
                        <a href="logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Cards de Estatísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon products">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-info">
                    <h3>0</h3>
                    <p>Total de Produtos</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon sales">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3>R$ 0</h3>
                    <p>Vendas do Mês</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon users">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>0</h3>
                    <p>Clientes Ativos</p>
                </div>
            </div>
        </div>

        <!-- Card Principal com Formulário -->
        <div class="main-card">
            <div class="card-header">
                <h2><i class="fas fa-plus-circle"></i> Cadastro de Produtos</h2>
            </div>
            <div class="card-body">
                <?php
                // Debug temporário
                if(isset($_SESSION['produtos'])){
                    echo "<!-- Produtos na sessão: ".count($_SESSION['produtos'])." -->";
                }
                ?>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Nome do Produto</label>
                                <input type="text" class="form-control" name="productName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Preço</label>
                                <input type="number" class="form-control" name="productPrice" step="0.01" min="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" name="productDescription" rows="3"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Quantidade</label>
                        <input type="number" class="form-control" name="productQuantity" min="1" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Produto
                    </button>
                </form>

                <!-- Debug temporário -->
                <div style="display:none">
                    <?php
                    echo "<pre>";
                    print_r($_SESSION);
                    echo "</pre>";
                    ?>
                </div>
            </div>
        </div>

        <!-- Lista de produtos -->
        <div class="products-container">
            <div class="products-header">
                <h3><i class="fas fa-boxes me-2"></i>Produtos Cadastrados</h3>
            </div>

            <?php 
            $produtos = carregarProdutos();
            if (!empty($produtos)): ?>
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Preço</th>
                            <th>Quantidade</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($produto['productName']); ?></td>
                                <td><?php echo htmlspecialchars($produto['productDescription']); ?></td>
                                <td>R$ <?php echo htmlspecialchars($produto['productPrice']); ?></td>
                                <td><?php echo htmlspecialchars($produto['productQuantity']); ?></td>
                                <td class="actions">
                                    <a href="editar-produto.php?id=<?php echo $produto['id']; ?>" class="edit-btn">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="excluir-produto.php?id=<?php echo $produto['id']; ?>" class="delete-btn" onclick="return confirm('Tem certeza que deseja excluir este produto?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>Nenhum Produto Cadastrado</h3>
                    <p>Use o formulário acima para começar a cadastrar seus produtos.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/theme.js"></script>
    <script>
    function cadastrarProduto(event) {
        event.preventDefault();
        
        const form = document.getElementById('formProduto');
        const formData = new FormData(form);
        formData.append('ajax_cadastro', '1');
        
        fetch('index.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recarrega a página para mostrar o novo produto
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao cadastrar produto');
        });
        
        return false;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const closeButtons = document.querySelectorAll('.close-btn');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const alert = this.parentElement;
                alert.style.animation = 'fadeOut 0.3s ease forwards';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            });
        });
    });
    </script>
</body>
</html>