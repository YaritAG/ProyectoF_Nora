<?php
require '../admin/db.php'; // Archivo de conexión a la base de datos

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Verifica si el usuario está logueado y tiene rol de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../../templates/menu.php');
    exit;
}

$conn = getConexion();

// Consultar los géneros que han sido agregados (Agregado = 1)
$stmt = $conn->prepare("SELECT Nombre, Descripcion, Imagen FROM tgenero WHERE Agregado = 1 ORDER BY Nombre ASC");
$stmt->execute();
$generos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mi Perfil | MiBiblio</title>
    <link rel="stylesheet" href="static/perfil.css">

    <!-- Quicksand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="container">

        <header>
            <?php include 'header-secciones.php'; ?>
        </header>

        <div class="seccion-principal">
            <h1>Mi perfil</h1>
                <h3>Hola, soy</h3>
        </div>
        
    </div>
</body>
</html>