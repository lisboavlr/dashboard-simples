<?php
//chama o arquivo abaixo nesta tela
include "verificar-autenticacao.php";
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
</head>
<body>
    <div class="dashboard-container">
        <!-- Header da Dashboard -->
        <header class="dashboard-header">
            <div class="dashboard-title">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard de Produtos
            </div>
            <div class="user-menu">
                <span class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <?php echo $_SESSION['nome'] ?? 'Usuário'; ?>
                </span>
                <a href="logout.php" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    Sair
                </a>
            </div>
        </header>

        <!-- Cards de Estatísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon products">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-info">
                    <h3>150</h3>
                    <p>Total de Produtos</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon sales">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3>R$ 45.500</h3>
                    <p>Vendas do Mês</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon users">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>1.250</h3>
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
                <form action="processar_produto.php" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-tag"></i> Nome do Produto
                                </label>
                                <input type="text" class="form-control" name="nome" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-dollar-sign"></i> Preço
                                </label>
                                <input type="number" class="form-control" name="preco" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-align-left"></i> Descrição
                        </label>
                        <textarea class="form-control" name="descricao" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Produto
                    </button>
                </form>
            </div>
        </div>

        <!-- Tabela de Produtos -->
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-box"></i> Produto</th>
                        <th><i class="fas fa-dollar-sign"></i> Preço</th>
                        <th><i class="fas fa-info-circle"></i> Status</th>
                        <th><i class="fas fa-cogs"></i> Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Seu código PHP para listar produtos aqui
                    // Exemplo de uma linha:
                    ?>
                    <tr>
                        <td>1</td>
                        <td>Produto Exemplo</td>
                        <td>R$ 99,90</td>
                        <td>
                            <span class="status-badge status-active">
                                <i class="fas fa-check-circle"></i> Ativo
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="editar.php?id=1" class="btn-action btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="excluir.php?id=1" class="btn-action btn-delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>