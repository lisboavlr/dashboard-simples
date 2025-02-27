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

// Função para encontrar um cliente pelo ID
function encontrarCliente($id) {
    $clientes = carregarClientes();
    foreach ($clientes as $cliente) {
        if (isset($cliente['id']) && intval($cliente['id']) === intval($id)) {
            return $cliente;
        }
    }
    return null;
}

// Verifica se foi passado um ID válido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['erro'] = "ID do cliente não fornecido";
    header("Location: clientes.php");
    exit();
}

$id = intval($_GET['id']);
$cliente = encontrarCliente($id);

if (!$cliente) {
    $_SESSION['erro'] = "Cliente não encontrado";
    header("Location: clientes.php");
    exit();
}

// Processamento do formulário de edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientes = carregarClientes();
    
    // Atualiza os dados do cliente
    foreach ($clientes as &$c) {
        if (isset($c['id']) && intval($c['id']) === $id) {
            $c['nome'] = $_POST['nome'];
            $c['email'] = $_POST['email'];
            $c['telefone'] = $_POST['telefone'];
            $c['cpf'] = $_POST['cpf'];
            $c['endereco'] = $_POST['endereco'];
            break;
        }
    }
    
    salvarClientes($clientes);
    
    $_SESSION['sucesso'] = "Cliente atualizado com sucesso!";
    header("Location: clientes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="css/dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Header da Dashboard -->
        <header class="dashboard-header">
            <div class="dashboard-title">
                <i class="fa-solid fa-user-edit"></i>
                Editar Cliente
            </div>
            <div class="header-actions">
                <a href="clientes.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Voltar para Lista
                </a>
            </div>
        </header>

        <!-- Card Principal com Formulário -->
        <div class="main-card">
            <div class="card-header">
                <h2><i class="fas fa-user-edit"></i> Editar Cliente</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" name="nome" value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($cliente['email']); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Telefone</label>
                                <input type="tel" class="form-control" name="telefone" value="<?php echo htmlspecialchars($cliente['telefone']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">CPF</label>
                                <input type="text" class="form-control" name="cpf" value="<?php echo htmlspecialchars($cliente['cpf']); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Endereço Completo</label>
                        <textarea class="form-control" name="endereco" rows="3" required><?php echo htmlspecialchars($cliente['endereco']); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                </form>
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
    </script>
</body>
</html> 
</html> 