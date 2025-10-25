<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (isset($_GET['id'])) {
    $banner_id = $_GET['id'];
    
    // Buscar banner
    $stmt = $pdo->prepare("SELECT * FROM banners WHERE id = ? AND status = 'ativo'");
    $stmt->execute([$banner_id]);
    $banner = $stmt->fetch();
    
    if ($banner) {
        // Registrar clique
        registrarCliqueBanner($banner_id);
        
        // Redirecionar para o link do banner
        if ($banner['link']) {
            header("Location: " . $banner['link']);
        } else {
            header("Location: index.php");
        }
        exit;
    }
}

// Se não encontrou o banner, redirecionar para home
header("Location: index.php");
exit;
?>