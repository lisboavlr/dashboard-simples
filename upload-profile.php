<?php

// Se um novo nome foi fornecido, atualiza
if (!empty($_POST['name']) && $_POST['name'] !== $userData['name']) {
    $name = trim($_POST['name']);
    $sql = "UPDATE users SET name = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $userId]);
}

// Processa a foto
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    // ... código de processamento da foto ...
} 