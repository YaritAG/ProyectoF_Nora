<?php
session_start();

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

    <!-- Quicksand -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&display=swap" 
        rel="stylesheet">
</head>
<body>
    <nav class="barra-superior">
        <div class="botones-navbar">
            <a href="verLibros.php" class="btn-verLibros">Ver Libros</a>
            <a href="verPrestamos.php" class="btn-verPrestamos">Ver Prestamos</a>
            <a href="../../templates/menu.php" class="btn-menu">Menú Principal</a>
            <a href="ventanaAdmin.php" class="btn-admin">Menu de Admin</a>
            <a href="verUsuarios.php" class="btn-users">Ver Usuarios</a>
        </div>
        
        <div class="logout">
            <a href="../public/user/perfil.php" class="btn-perfil">Mi Perfil</a>
            <a href="../public/user/login.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container">
        <h1>Bienvenido admin</h1>
    </div>
</body>
</html>