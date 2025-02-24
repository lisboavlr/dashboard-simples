<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

function carregarProdutos() {
    $arquivo = 'produtos.json';
    if (file_exists($arquivo)) {
        $json = file_get_contents($arquivo);
        return json_decode($json, true) ?: [];
    }
    return [];
}

function salvarProdutos($produtos) {
    $arquivo = 'produtos.json';
    file_put_contents($arquivo, json_encode($produtos, JSON_PRETTY_PRINT));
}

$produtos = carregarProdutos();
$id = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : null);

if (!$id) {
    $_SESSION['erro'] = "ID do produto não especificado!";
    header("Location: index.php");
    exit();
}

$produto = null;
$index = null;
foreach ($produtos as $key => $p) {
    if ($p['id'] == $id) {
        $produto = $p;
        $index = $key;
        break;
    }
}

if (!$produto) {
    $_SESSION['erro'] = "Produto não encontrado!";
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produtos[$index] = [
        'id' => $id,
        'productName' => $_POST['productName'],
        'productPrice' => floatval($_POST['productPrice']),
        'productDescription' => $_POST['productDescription'],
        'productQuantity' => intval($_POST['productQuantity']),
        'data_cadastro' => $produto['data_cadastro'],
        'data_atualizacao' => date('Y-m-d H:i:s')
    ];
    
    salvarProdutos($produtos);
    $_SESSION['sucesso'] = "Produto atualizado com sucesso!";
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/dashboard.css" rel="stylesheet">
    <link href="css/editar-produto.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Header da Dashboard -->
        <header class="dashboard-header">
            <div class="dashboard-title">
                <i class="fas fa-edit"></i>
                Editar Produto
            </div>
            <div class="header-actions">
                <div class="user-menu">
                    <span class="user-info">
                        <?php if (!empty($_SESSION['foto_perfil'])): ?>
                            <img src="uploads/perfil/<?php echo $_SESSION['foto_perfil']; ?>" class="profile-pic" alt="Foto de Perfil">
                        <?php else: ?>
                            <i class="fas fa-user-circle"></i>
                        <?php endif; ?>
                        <?php echo $_SESSION['nome'] ?? 'Usuário'; ?>
                    </span>
                </div>
            </div>
        </header>

        <!-- Breadcrumb -->
        <div class="breadcrumb-wrapper">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="breadcrumb-item active">Editar Produto</li>
                </ol>
            </nav>
        </div>

        <!-- Container Principal -->
        <div class="content-wrapper">
            <?php if (isset($mensagem)): ?>
                <div class="alert alert-<?php echo $tipo; ?> fade show" role="alert">
                    <i class="fas fa-<?php echo $tipo === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo $mensagem; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="edit-card">
                <div class="edit-card-header">
                    <h2><i class="fas fa-box-open"></i> Detalhes do Produto</h2>
                </div>
                <div class="edit-card-body">
                    <form class="edit-form" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-tag"></i>
                                    Nome do Produto
                                </label>
                                <input type="text" name="productName" value="<?php echo htmlspecialchars($produto['productName']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-dollar-sign"></i>
                                    Preço
                                </label>
                                <input type="number" name="productPrice" step="0.01" value="<?php echo htmlspecialchars($produto['productPrice']); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                <i class="fas fa-align-left"></i>
                                Descrição
                            </label>
                            <textarea name="productDescription" rows="4"><?php echo htmlspecialchars($produto['productDescription']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>
                                <i class="fas fa-cubes"></i>
                                Quantidade em Estoque
                            </label>
                            <input type="number" name="productQuantity" value="<?php echo htmlspecialchars($produto['productQuantity']); ?>" required>
                        </div>

                        <div class="button-group">
                            <button type="submit" class="save-btn">
                                <i class="fas fa-save"></i>
                                Salvar Alterações
                            </button>
                            <a href="index.php" class="cancel-btn">
                                <i class="fas fa-times"></i>
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 