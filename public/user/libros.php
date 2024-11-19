<?php
// Mostrar errores en el entorno de desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar la sesión
session_start();

// Archivo de conexión a la base de datos (solo una vez)
require '../admin/db.php';

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../../templates/menu.php');
    exit;
}

// Establecer la conexión a la base de datos
$conn = getConexion();

// Verificar si se envió el formulario de préstamo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? null; // ID del usuario enviado desde el formulario
    $libroId = $_POST['libro_id'] ?? null; // ID del libro enviado desde el formulario
    $fechaPrestamo = date('Y-m-d H:i:s'); // Fecha actual

    if ($userId && $libroId) {
        try {
            // Insertar el préstamo en la tabla `tprestamo`
            $stmt = $conn->prepare("
                INSERT INTO tprestamo (fecha_prestamo, TPersonas_id_Personas, TLibros_id_Libro, Devuelto) 
                VALUES (:fecha_prestamo, :user_id, :libro_id, 0)
            ");
            $stmt->bindParam(':fecha_prestamo', $fechaPrestamo);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':libro_id', $libroId);
            $stmt->execute();

            $_SESSION['mensaje'] = "¡Préstamo registrado exitosamente!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error al registrar el préstamo: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Hubo un error al registrar el préstamo. Verifique los datos enviados.";
    }
}

// Redirigir a la página de préstamos y detener el script
header('Location: prestamos.php');
exit;

// Consultar todos los libros
$stmt = $conn->prepare("SELECT * FROM tlibros");
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
            <h3>Género de <?= htmlspecialchars($libro['Genero']) ?></h3>
                <div class="libro">
                    <div class="seccion-imagen">
                        <img src="../../assets/imgs/menu/<?= htmlspecialchars($libro['Nombre']) ?>.jpg" alt="" class="img-libro">
                    </div>
                    <p class="titulo-libro"><?= htmlspecialchars($libro['Nombre']) ?></p>
                    <ul class="lista-características">
                        <li><b>Autor:</b> <?= htmlspecialchars($libro['Autor']) ?></li>
                        <li><b>Páginas:</b> <?= htmlspecialchars($libro['Paginas']) ?></li>
                        <li><b>Año:</b> <?= htmlspecialchars($libro['Ano']) ?></li>
                        <li><b>Género:</b> <?= htmlspecialchars($libro['Genero']) ?></li>
                        <li><b>Editorial:</b> <?= htmlspecialchars($libro['Editorial']) ?></li>
                        <li><b>Ejemplares:</b> <?= htmlspecialchars($libro['Ejemplar']) ?></li>
                    </ul>
            
                    <div class="sintesis">
                        <p>"<?= htmlspecialchars($libro['Sinopsis'] ?? 'Sin sinopsis disponible.') ?>"</p>
                    </div>
            
                    <div class="buttons">
                        <form action="prestamos.php" method="POST">
                            <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                            <input type="hidden" name="libro_id" value="<?= $libro['id_Libro'] ?>"> <!-- ID del libro dinámico -->
                            <input class="btn-solicitar" type="submit" value="Solicitar Préstamo">
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</html>