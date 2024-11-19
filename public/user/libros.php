<?php
// Mostrar errores en el entorno de desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar la sesión
session_start();

// Archivo de conexión a la base de datos
require '../admin/db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../templates/menu.php');
    exit;
}

// Establecer la conexión a la base de datos
$conn = getConexion();

// Verificar si se envió un libro desde `verLibros.php`
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libroId = $_POST['libro_id'] ?? null; // ID del libro enviado desde el formulario

    if ($libroId) {
        try {
            // Marcar el libro como enviado en la base de datos
            $stmt = $conn->prepare("UPDATE tlibros SET Enviado = 1 WHERE id_Libro = :libro_id");
            $stmt->bindParam(':libro_id', $libroId, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['mensaje'] = "¡Libro enviado exitosamente!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error al enviar el libro: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "ID de libro no válido.";
    }

    // Redirigir para evitar reenvío del formulario
    header('Location: libros.php');
    exit;
}

// Consultar todos los libros marcados como enviados
$stmt = $conn->prepare("
    SELECT l.id_Libro, l.Nombre, l.Ejemplar, l.Editorial, l.Paginas, l.Año, l.Sintesis AS Sinopsis,
           g.Nombre AS Genero, a.Nombre AS Autor
    FROM tlibros l
    LEFT JOIN tautor_has_tlibros al ON l.id_Libro = al.TLibros_id_Libro
    LEFT JOIN tautor a ON al.TAutor_id_Autor = a.id_Autor
    LEFT JOIN tlibros_has_tgenero lg ON l.id_Libro = lg.TLibros_id_Libro
    LEFT JOIN tgenero g ON lg.TGenero_id_Genero = g.id_Genero
    WHERE l.Enviado = 1 -- Solo mostrar libros enviados
");
$stmt->execute();
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros | MyBiblio</title>
    <link rel="stylesheet" href="static/libros.css?=ver<?php echo time(); ?>">
    
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

        <?php if (isset($_SESSION['mensaje'])): ?>
            <p class="mensaje"><?= $_SESSION['mensaje'] ?></p>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>
        
        <div class="seccion-principal">
            <h1>Libros</h1>
            <h3>Estos son todos los libros los cuales puedes explorar</h3>
            <br>
            
            <div class="seccion-libro">
                <h3>Género de Fantasía</h3>
                    <div class="libro">
                    <div class="seccion-imagen">
                        <img src="../../assets/imgs/menu/magodeoz.jpg" alt="" class="img-libro">
                    </div>    
                        <p class="titulo-libro">El Maravilloso Mundo del Mago de OZ</p>
                        <ul class="lista-características">
                            <li><b>Autor:</b></li>
                            <li><b>Páginas:</b></li>
                            <li><b>Año:</b></li>
                            <li><b>Género:</b></li>
                            <li><b>Editorial:</b></li>
                            <li><b>Ejemplares:</b></li>
                        </ul>

                        <div class="sintesis">
                            <p>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure 
                                dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non 
                                proident, sunt in culpa qui officia deserunt mollit anim id est laborum."</p>
                        </div>

                    <div class="buttons">
                        <form action="prestamos.php" method="POST">
                            <!-- ID del usuario -->
                            <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                            <!-- ID del libro -->
                            <input type="hidden" name="libro_id" value="1"> <!-- Cambiar a ID dinámico del libro -->
                            <input class="btn-solicitar" type="submit" value="Solicitar Préstamo">
                        </form>
                    </div>

                    </div>

                    <div class="libro">
                        <img src="" alt="">
                        <p class="titulo-libro">Pedro Páramo</p>
                        <ul class="lista-características">
                            <li><b>Autor:</b></li>
                            <li><b>Páginas:</b></li>
                            <li><b>Año:</b></li>
                            <li><b>Género:</b></li>
                            <li><b>Editorial:</b></li>
                            <li><b>Ejemplares:</b></li>
                        </ul>

                        <div class="sintesis">
                            <p>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure 
                                dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non 
                                proident, sunt in culpa qui officia deserunt mollit anim id est laborum."</p>
                        </div>
                    </div>

                    <div class="libro">
                        <img src="" alt="">
                        <p class="titulo-libro">La Odisea</p>
                        <ul class="lista-características">
                            <li><b>Autor:</b></li>
                            <li><b>Páginas:</b></li>
                            <li><b>Año:</b></li>
                            <li><b>Género:</b></li>
                            <li><b>Editorial:</b></li>
                            <li><b>Ejemplares:</b></li>
                        </ul>

                        <div class="sintesis">
                            <p>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure 
                                dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non 
                                proident, sunt in culpa qui officia deserunt mollit anim id est laborum."</p>
                        </div>
                    </div>
            </div>

            <div class="seccion-libro">
                <h3>Género de Novela Ciencia Ficcón</h3>
                    <div class="libro">
                        <img src="" alt="">
                        <p class="titulo-libro">Boulevard</p>
                        <ul class="lista-características">
                            <li><b>Autor:</b></li>
                            <li><b>Páginas:</b></li>
                            <li><b>Año:</b></li>
                            <li><b>Género:</b></li>
                            <li><b>Editorial:</b></li>
                            <li><b>Ejemplares:</b></li>
                        </ul>

                        <div class="sintesis">
                            <p>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure 
                                dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non 
                                proident, sunt in culpa qui officia deserunt mollit anim id est laborum."</p>
                        </div>
                    </div>

                    <div class="libro">
                        <img src="" alt="">
                        <p class="titulo-libro">Don Quijote de la Mancha</p>
                        <ul class="lista-características">
                            <li><b>Autor:</b></li>
                            <li><b>Páginas:</b></li>
                            <li><b>Año:</b></li>
                            <li><b>Género:</b></li>
                            <li><b>Editorial:</b></li>
                            <li><b>Ejemplares:</b></li>
                        </ul>

                        <div class="sintesis">
                            <p>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure 
                                dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non 
                                proident, sunt in culpa qui officia deserunt mollit anim id est laborum."</p>
                        </div>
                    </div>
            </div>

            <div class="seccion-libro">
                <?php foreach ($libros as $libro): ?>
                    <h3>Género de <?= htmlspecialchars($libro['Genero'] ?? 'Desconocido') ?></h3>
                    <div class="libro">
                        <div class="seccion-imagen">
                            <img src="../../assets/imgs/menu/<?= htmlspecialchars($libro['Nombre']) ?>.jpg"
                                alt="Imagen de <?= htmlspecialchars($libro['Nombre']) ?>" class="img-libro">
                        </div>
                        <p class="titulo-libro"><?= htmlspecialchars($libro['Nombre']) ?></p>
                        <ul class="lista-características">
                            <li><b>Autor:</b> <?= htmlspecialchars($libro['Autor'] ?? 'Desconocido') ?></li>
                            <li><b>Páginas:</b> <?= htmlspecialchars($libro['Paginas'] ?? 'Desconocido') ?></li>
                            <li><b>Año:</b> <?= htmlspecialchars($libro['Año'] ?? 'Desconocido') ?></li>
                            <li><b>Género:</b> <?= htmlspecialchars($libro['Genero'] ?? 'Desconocido') ?></li>
                            <li><b>Editorial:</b> <?= htmlspecialchars($libro['Editorial'] ?? 'Desconocido') ?></li>
                            <li><b>Ejemplares:</b> <?= htmlspecialchars($libro['Ejemplar'] ?? 'Desconocido') ?></li>
                        </ul>
                        <div class="sintesis">
                            <p>"<?= htmlspecialchars($libro['Sinopsis'] ?? 'Sin sinopsis disponible.') ?>"</p>
                        </div>
                        <div class="buttons">
                            <form action="libros.php" method="POST">
                                <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                                <input type="hidden" name="libro_id" value="<?= htmlspecialchars($libro['id_Libro']) ?>">
                                <!-- ID del libro dinámico -->
                                <input class="btn-solicitar" type="submit" value="Solicitar Préstamo"
                                    onclick="return confirm('¿Estás seguro de que deseas solicitar este préstamo?')">
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</html>