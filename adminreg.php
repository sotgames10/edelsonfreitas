<?php
require_once 'includes/config.php';

// Criar usuário admin
$username = "edelson";
$password = password_hash("121875Tr", PASSWORD_DEFAULT);
$nome = "Edelson Freitas";
$email = "contato@edelsonfreitas.com";

$stmt = $pdo->prepare("INSERT INTO usuarios (username, password, nome, email) VALUES (?, ?, ?, ?)");
$stmt->execute([$username, $password, $nome, $email]);

echo "Usuário admin criado com sucesso!";
echo "<br>Username: edelson";
echo "<br>Password: 121875Tr";
?>