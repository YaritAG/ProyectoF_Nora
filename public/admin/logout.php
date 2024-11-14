<?php
require_once 'db.php';
session_start();
session_unset(); // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión
header("Location: ../user/login.php"); // Redirige al usuario a la página de login
exit;
?>