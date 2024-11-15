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


include '../../templates/a.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Usuarios </title>
    <link rel="stylesheet" href="static/tablasAdmin.css">

    <!-- icon para el buscador -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>
    <div class="container">
        <h1>Tabla de Usuarios</h1>

        <div class="seccion-tabla">
            <div class="buscador">
                <input type="search" class="buscar" placeholder="Buscar..." name="query" aria-label="Buscar">
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </div>
</body>
</html>