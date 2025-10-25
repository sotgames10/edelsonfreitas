<?php
require_once '../includes/auth.php';

// Fazer logout
fazerLogout();

// Redirecionar para login
header("Location: login.php");
exit;
?>