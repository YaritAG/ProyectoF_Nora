<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Verifica si el usuario está logueado y tiene rol de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../../templates/menu.php'); // Redirige si no es administrador
    exit;
}

// Contenido exclusivo para administradores
echo "Bienvenido al panel de administración";

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="static/admin.css">

    <!-- Quicksand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Quicksand:wght@300..700&display=swap"
        rel="stylesheet">
</head>

<body>
    <nav class="barra-superior" style="background-color: #333; color: #fff; padding: 10px;">
    <!-- contenido de la barra de navegación -->
    </nav>
    <!-- Incluye la barra de navegación desde el archivo headerAdmin.php -->
    <?php include '../../assets/partials/headerAdmin.php'; ?>

    <div class="container">
        <h1>Bienvenido admin</h1>
        <!-- Aquí puedes agregar más contenido exclusivo para el administrador -->
    </div>

</body>

</html>