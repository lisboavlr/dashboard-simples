<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Função para carregar produtos do arquivo JSON
function carregarProdutos() {
    $arquivo = 'dados/produtos.json';
    if (!file_exists('dados')) {
        mkdir('dados', 0777, true);
    }
    if (file_exists($arquivo)) {
        $json = file_get_contents($arquivo);
        return json_decode($json, true) ?: [];
    }
    return [];
}

// Função para salvar produtos no arquivo JSON
function salvarProdutos($produtos) {
    $arquivo = 'dados/produtos.json';
    if (!file_exists('dados')) {
        mkdir('dados', 0777, true);
    }
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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="css/dashboard.css" rel="stylesheet">
    <style>
    .alert-success {
        padding: 1rem 1.5rem;
        margin: 1.5rem auto;
        max-width: 800px;
        border-left: 4px solid #28a745;
        background-color: #f8fff9;
        color: #1e7e34;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 1rem;
        font-weight: 500;
        position: relative;
        animation: slideInDown 0.3s ease-out, fadeIn 0.3s ease-out;
    }

    .alert-success::before {
        content: '\f058';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        margin-right: 12px;
        font-size: 1.2rem;
        color: #28a745;
    }

    .alert-success .close-btn {
        background: none;
        border: none;
        color: #1e7e34;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        opacity: 0.7;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
    }

    .alert-success .close-btn:hover {
        opacity: 1;
        background-color: rgba(40, 167, 69, 0.1);
    }

    @keyframes slideInDown {
        from {
            transform: translateY(-20px);
        }
        to {
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
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
            <div class="alert-success" role="alert">
                <div class="alert-content">
                    <?php echo $_SESSION['sucesso']; ?>
                </div>
                <button type="button" class="close-btn" aria-label="Fechar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php unset($_SESSION['sucesso']); ?>
        <?php endif; ?>

        <!-- Header da Dashboard -->
        <header class="dashboard-header">
            <div class="dashboard-title">
                <i class="fa-solid fa-house-laptop"></i>
                Dashboard
            </div>
            <div class="header-actions">
                <a href="clientes.php" class="btn btn-outline-primary me-2">
                    <i class="fas fa-users"></i> Clientes
                </a>
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