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
<head>
    <link rel="stylesheet" href="static/admin.css?ver=<?php echo time(); ?>">
</head>
    <div class="container">
        <h1>Bienvenido admin</h1>
        <!-- Aquí puedes agregar más contenido exclusivo para el administrador -->
    </div>

</body>

</html>