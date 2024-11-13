<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../../templates/menu.html'); // Redirige si no es administrador
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
</head>
<body>
    <nav class="barra-superior">
        <div class="botones-navbar">
            <a href="verLibros.php">Ver Libros</a>
            <a href="verPrestamos.php">Ver Prestamos</a>
            <a href="../../templates/menu.html">Menú Principal</a>
            <a href="ventanaAdmin.php">Menu de Admin</a>
            <a href="verUsuarios.php">Ver Usuarios</a>
        </div>
        
        <div class="logout">
            <div class="logout">
                <a href="../public/user/perfil.php">Mi Perfil</a>
                <a href="../public/user/login.php">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>Bienvenido admin</h1>
    </div>
</body>
</html>