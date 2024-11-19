<?php
// ini.php - Archivo de configuración global

// ----------------------------------------
// Mostrar errores en el entorno de desarrollo
// ----------------------------------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ----------------------------------------
// Iniciar sesión
// ----------------------------------------
session_start();

// ----------------------------------------
// Configuración de la base de datos
// ----------------------------------------
require_once __DIR__ . '/db.php'; // Incluye el archivo de conexión a la BD

// ----------------------------------------
// Verificar si el usuario está logueado
// ----------------------------------------
function verificarSesion()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../public/user/login.php');
        exit;
    }
}

// ----------------------------------------
// Redirigir según el rol del usuario
// ----------------------------------------
function redirigirPorRol()
{
    if (isset($_SESSION['user_role'])) {
        if ($_SESSION['user_role'] === 'admin') {
            header('Location: ../admin/menu.php');
        } elseif ($_SESSION['user_role'] === 'user') {
            header('Location: ../user/menu.php');
        }
        exit;
    }
}

// ----------------------------------------
// Configurar constantes del proyecto
// ----------------------------------------
define('BASE_URL', 'http://localhost/'); // Cambia a tu dominio real
define('ASSETS_URL', BASE_URL . 'assets/');

// ----------------------------------------
// Configuración de zona horaria
// ----------------------------------------
date_default_timezone_set('America/Mexico_City'); // Ajusta según tu región

// ----------------------------------------
// Mensajes globales
// ----------------------------------------
function obtenerMensaje()
{
    if (isset($_SESSION['mensaje'])) {
        $mensaje = $_SESSION['mensaje'];
        unset($_SESSION['mensaje']);
        return $mensaje;
    }
    return null;
}

function obtenerError()
{
    if (isset($_SESSION['error'])) {
        $error = $_SESSION['error'];
        unset($_SESSION['error']);
        return $error;
    }
    return null;
}
?>