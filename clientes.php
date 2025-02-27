<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "verificar-autenticacao.php";

// Função para carregar clientes do arquivo JSON
function carregarClientes() {
    $arquivo = 'dados/clientes.json';
    if (file_exists($arquivo)) {
        $json = file_get_contents($arquivo);
        return json_decode($json, true) ?: [];
    }
    return [];
}

// Função para salvar clientes no arquivo JSON
function salvarClientes($clientes) {
    $arquivo = 'dados/clientes.json';
    file_put_contents($arquivo, json_encode($clientes, JSON_PRETTY_PRINT));
}

// Processamento do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientes = carregarClientes();
    
    // Encontra o maior ID atual
    $maxId = 0;
    foreach ($clientes as $cliente) {
        if (isset($cliente['id'])) {
            $maxId = max($maxId, intval($cliente['id']));
        }
    }
    
    $novo_cliente = array(
        'id' => $maxId + 1,
        'nome' => $_POST['nome'],
        'email' => $_POST['email'],
        'telefone' => $_POST['telefone'],
        'cpf' => $_POST['cpf'],
        'endereco' => $_POST['endereco'],
        'data_cadastro' => date('Y-m-d H:i:s')
    );
    
    $clientes[] = $novo_cliente;
    salvarClientes($clientes);
    
    $_SESSION['sucesso'] = "Cliente cadastrado com sucesso!";
    header("Location: clientes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Cadastro de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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

    /* Estilos para os botões de ação */
    .actions {
        width: 120px;
        text-align: center;
    }

    .edit-btn, .delete-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        margin: 0 3px;
        border-radius: 4px;
        color: #fff;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .edit-btn {
        background-color: #ffc107;
        color: #000;
    }

    .edit-btn:hover {
        background-color: #ffca2c;
        transform: translateY(-2px);
        color: #000;
        text-decoration: none;
    }

    .delete-btn {
        background-color: #dc3545;
        color: #fff;
    }

    .delete-btn:hover {
        background-color: #bb2d3b;
        transform: translateY(-2px);
        color: #fff;
        text-decoration: none;
    }

    .edit-btn i, .delete-btn i {
        font-size: 16px;
    }

    /* Estilos da tabela */
    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .table thead th {
        background-color: #f8f9fa !important;
        border-bottom: 2px solid #dee2e6 !important;
        font-weight: 600 !important;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa !important;
        transition: background-color 0.2s ease;
    }

    .table-responsive {
        overflow-x: auto;
        padding: 0;
        margin: 0;
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

    /* Estilos para os botões */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }

    /* Estilos da nova tabela */
    .table-container {
        margin: 20px 0;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .table {
        width: 100%;
        background: white;
        border: none;
        border-collapse: collapse;
    }

    .table thead {
        background: #f8f9fa;
    }

    .table th {
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        padding: 15px;
        border-bottom: 2px solid #dee2e6;
    }

    .table td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #eee;
        color: #333;
        font-size: 14px;
    }

    /* Coluna de ações */
    .actions {
        width: 160px;
        text-align: center;
    }

    /* Botões de ação */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
        align-items: center;
    }

    .action-btn {
        width: 38px;
        height: 38px;
        border-radius: 6px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .action-btn i {
        font-size: 16px;
    }

    .action-btn.edit {
        background-color: #ffc107;
        color: #000;
    }

    .action-btn.edit:hover {
        background-color: #ffca2c;
        transform: translateY(-2px);
    }

    .action-btn.delete {
        background-color: #dc3545;
        color: white;
    }

    .action-btn.delete:hover {
        background-color: #bb2d3b;
        transform: translateY(-2px);
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .table td, .table th {
            padding: 10px;
        }
        
        .action-btn {
            width: 34px;
            height: 34px;
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
                <i class="fa-solid fa-users"></i>
                Cadastro de Clientes
            </div>
            <div class="header-actions">
                <a href="index.php" class="btn btn-outline-primary">
                    <i class="fas fa-home"></i> Voltar para Dashboard
                </a>
            </div>
        </header>

        <!-- Card Principal com Formulário -->
        <div class="main-card">
            <div class="card-header">
                <h2><i class="fas fa-user-plus"></i> Novo Cliente</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" name="nome" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Telefone</label>
                                <input type="tel" class="form-control" name="telefone" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">CPF</label>
                                <input type="text" class="form-control" name="cpf" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Endereço Completo</label>
                        <textarea class="form-control" name="endereco" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cadastrar Cliente
                    </button>
                </form>
            </div>
        </div>

        <!-- Lista de Clientes -->
        <div class="clients-container mt-4">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-users me-2"></i>Clientes Cadastrados</h3>
                </div>
                <div class="card-body p-0">
                    <?php 
                    $clientes = carregarClientes();
                    if (!empty($clientes)): ?>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">ID</th>
                                        <th>Nome</th>
                                        <th>E-mail</th>
                                        <th>Telefone</th>
                                        <th>CPF</th>
                                        <th class="actions">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <tr>
                                            <td><?php echo intval($cliente['id']); ?></td>
                                            <td><?php echo htmlspecialchars($cliente['nome']); ?></td>
                                            <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                                            <td><?php echo htmlspecialchars($cliente['telefone']); ?></td>
                                            <td><?php echo htmlspecialchars($cliente['cpf']); ?></td>
                                            <td class="actions">
                                                <a href="editar-cliente.php?id=<?php echo intval($cliente['id']); ?>" class="edit-btn" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="excluir-cliente.php?id=<?php echo intval($cliente['id']); ?>" class="delete-btn" onclick="return confirm('Tem certeza que deseja excluir este cliente?')" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h4>Nenhum Cliente Cadastrado</h4>
                            <p class="text-muted">Use o formulário acima para começar a cadastrar seus clientes.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
    $(document).ready(function(){
        // Máscaras para os campos
        $('input[name="telefone"]').mask('(00) 00000-0000');
        $('input[name="cpf"]').mask('000.000.000-00');
    });

    // Fechar alertas
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