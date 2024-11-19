<?php
require_once 'ini.php';

// Verificar si el usuario está logueado (si es necesario para esta página)
verificarSesion();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Verifica si el usuario está logueado y tiene rol de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../../templates/menu.php'); // Redirige si no es administrador
    exit;
}


include '../../templates/a.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Usuarios </title>
    <link rel="stylesheet" href="static/admin.css?ver=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <h1 clas="fade-in">Bienvenido Admin </h1><br>
        <h2 class="fade-in<">Selecciona en la barra Superior a donde quieras ir :) </h2>
    </div>
</body>
</html>