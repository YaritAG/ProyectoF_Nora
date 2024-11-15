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

require 'db.php'; // Incluye el archivo de conexión a la base de datos
$conn = getConexion();;

// Si se envía el formulario para agregar un libro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'];
    $ejemplar = $_POST['ejemplar'];
    $editorial = $_POST['editorial'];
    $genero = $_POST['genero'];
    $paginas = $_POST['paginas'];
    $anio = $_POST['anio'];

    $query = "INSERT INTO tlibros (nombre, ejemplar, editorial, genero, paginas, anio) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$nombre, $ejemplar, $editorial, $genero, $paginas, $anio]);

    header('Location: verLibros.php');
    exit;
}

// Si se envía el formulario para borrar un libro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrar'])) {
    $id = $_POST['id'];
    $query = "DELETE FROM tlibros WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);

    header('Location: verLibros.php');
    exit;
}

// Obtener todos los libros de la base de datos para mostrar en la tabla
$query = "SELECT * FROM tlibros";
$stmt = $conn->prepare($query);
$stmt->execute();
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../../templates/a.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Libros | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="static/tablasAdmin.css">
</head>

<body>
    <div class="container">
        <h1>Tabla de Libros</h1>

        <div class="seccion-tabla">

            <div class="seccion-buscador">
                <form method="GET" action="verUsuarios.php">
                    <input type="text" class="buscador" placeholder="Buscar..." name="query" aria-label="Buscar">
                    <button class="lupa" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <div class="inputs">
                <div class="editors">
                    <h3>Editar o Agregar Libro</h3>
                </div>
            </div>
        </div>
    </div>
</body>

</html>