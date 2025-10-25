<?php
session_start();

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'edelson_freitas');
define('DB_USER', 'root');
define('DB_PASS', '');

// Conexão com o banco
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Configurações do site
define('SITE_NAME', 'Black Angel - Edelson Freitas');
define('SITE_URL', 'http://localhost/edelson_freitas');
?>