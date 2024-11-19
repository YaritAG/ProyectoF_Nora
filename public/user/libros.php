<?php
session_start();
require '../admin/db.php'; // Conexión a la base de datos

$conn = getConexion();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../user/login.php');
    exit;
}

// Leer todos los géneros que fueron enviados
$stmtGeneros = $conn->prepare("SELECT id_Genero, Nombre, Descripcion, Imagen FROM tgenero WHERE Agregado = 1 ORDER BY Nombre ASC");
$stmtGeneros->execute();
$generos = $stmtGeneros->fetchAll(PDO::FETCH_ASSOC);

// Leer todos los libros asociados con cada género
$stmtLibros = $conn->prepare("
    SELECT l.id_Libro, l.Nombre, l.Ejemplar, l.Editorial, l.Paginas, l.Año, l.Sintesis AS Sinopsis,
           g.Nombre AS Genero, a.Nombre AS Autor, g.id_Genero
    FROM tlibros l
    LEFT JOIN tautor_has_tlibros al ON l.id_Libro = al.TLibros_id_Libro
    LEFT JOIN tautor a ON al.TAutor_id_Autor = a.id_Autor
    LEFT JOIN tlibros_has_tgenero lg ON l.id_Libro = lg.TLibros_id_Libro
    LEFT JOIN tgenero g ON lg.TGenero_id_Genero = g.id_Genero
    WHERE g.Agregado = 1
    ORDER BY g.Nombre ASC
");
$stmtLibros->execute();
$libros = $stmtLibros->fetchAll(PDO::FETCH_ASSOC);

// Agrupar los libros por género
$librosPorGenero = [];
foreach ($libros as $libro) {
    $librosPorGenero[$libro['id_Genero']][] = $libro;
}
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

               <!-- Listado de géneros -->
            <?php if (empty($generos)): ?>
                <p>No hay géneros disponibles en este momento.</p>
            <?php else: ?>
                <?php foreach ($generos as $genero): ?>
                    <div class="seccion-genero">
                        <h2><?= htmlspecialchars($genero['Nombre']) ?></h2>
                        <p><?= nl2br(htmlspecialchars($genero['Descripcion'])) ?></p>
            
                        <?php if (!empty($genero['Imagen'])): ?>
                            <img src="../../<?= htmlspecialchars($genero['Imagen']) ?>" alt="<?= htmlspecialchars($genero['Nombre']) ?>"
                                class="imagen-genero">
                        <?php endif; ?>
            
                        <!-- Listado de libros por género -->
                        <?php if (!empty($librosPorGenero[$genero['id_Genero']])): ?>
                            <div class="seccion-libros">
                                <?php foreach ($librosPorGenero[$genero['id_Genero']] as $libro): ?>
                                    <div class="libro-item">
                                        <h3><?= htmlspecialchars($libro['Nombre']) ?></h3>
                                        <ul>
                                            <li><b>Autor:</b> <?= htmlspecialchars($libro['Autor']) ?></li>
                                            <li><b>Páginas:</b> <?= htmlspecialchars($libro['Paginas']) ?></li>
                                            <li><b>Año:</b> <?= htmlspecialchars($libro['Año']) ?></li>
                                            <li><b>Editorial:</b> <?= htmlspecialchars($libro['Editorial']) ?></li>
                                            <li><b>Ejemplares:</b> <?= htmlspecialchars($libro['Ejemplar']) ?></li>
                                        </ul>
                                        <p><b>Sinopsis:</b> <?= htmlspecialchars($libro['Sinopsis']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>No hay libros disponibles para este género.</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
</html>