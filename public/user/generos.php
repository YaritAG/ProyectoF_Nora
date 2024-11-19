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
        <title>Géneros | MiBiblio</title>
        <link rel="stylesheet" href="static/generos.css">

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
            <h1>Géneros</h1>
            <h3>Selecciona a qué género quieres ingresar</h3>
    
            <!-- Galería de géneros -->
            <div class="gallery ">
                <!-- Género Fantasía -->
                <div class="sec-genero-libro">
                    <a href="/generos/fantasia.html" class="link-genero">
                        <div class="container-cards">
                            <div class="frente">
                                <img src="../../assets/imgs/menu/fantasia.png" alt="Imagen" class="img-genero">
                                <div class="inner">
                                    <p>FANTASIA</p>
                                </div>
                            </div>
                            <div class="detras">
                                <div class="inner">
                                    <p>El género de fantasía explora mundos imaginarios con magia, seres extraordinarios y
                                        aventuras épicas, transportando al
                                        lector a realidades fantásticas e irreales.</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
    
                <!-- Género de Misterio y Suspenso-->
                <div class="sec-genero-libro">
                    <a href="generos/misterioysus.html" class="link-genero">
                        <div class="container-cards">
                            <div class="frente">
                                <img src="../../assets/imgs/menu/suspenso.jpg" alt="Imagen" class="img-genero">
                                <div class="inner">
                                    <p>MISTERIO Y SUSPENSO</p>
                                </div>
                            </div>
                            <div class="detras">
                                <div class="inner">
                                    <p>El género de misterio y suspenso explora enigmas, crímenes y secretos, manteniendo al
                                        lector en tensión constante
                                        mientras descubre pistas y giros inesperados.</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
    
                <!-- Novela Romana -->
                <div class="sec-genero-libro">
                    <a href="generos/novelaroman.html" class="link-genero">
                        <div class="container-cards">
                            <div class="frente">
                                <img src="../../assets/imgs/menu/romantica.jpg" alt="Imagen" class="img-genero">
                                <div class="inner">
                                    <p>NOVELA ROMÁNTICA</p>
                                </div>
                            </div>
                            <div class="detras">
                                <div class="inner">
                                    <p>La novela romántica narra historias de amor, enfocándose en la conexión emocional y
                                        desafíos entre los protagonistas,
                                        generalmente con finales felices, destacando el poder del amor para superar
                                        obstáculos y transformar vidas.</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
    
                <!-- Novelas CF -->
                <div class="sec-genero-libro">
                    <a href="generos/novelascf.html" class="link-genero">
                        <div class="container-cards">
                            <div class="frente">
                                <img src="../../assets/imgs/menu/cf.jpg" alt="Imagen" class="img-genero">
                                <div class="inner">
                                    <p>NOVELAS CIENCIA FICCIÓN</p>
                                </div>
                            </div>
                            <div class="detras">
                                <div class="inner">
                                    <p>El género de ciencia ficción explora mundos futuristas, avances tecnológicos y
                                        fenómenos científicos, a menudo con
                                        elementos especulativos e imaginativos.</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
    
                <!-- Terror -->
                <div class="sec-genero-libro">
                    <a href="terror.html" class="link-genero">
                        <div class="container-cards">
                            <div class="frente">
                                <img src="../../assets/imgs/menu/terror.png" alt="Imagen" class="img-genero">
                                <div class="inner">
                                    <p>TERROR</p>
                                </div>
                            </div>
                            <div class="detras">
                                <div class="inner">
                                    <p>El género de terror busca provocar miedo y ansiedad en el lector, explorando lo
                                        sobrenatural, lo
                                        macabro y situaciones
                                        inquietantes.</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                
                <div class="seccion-libro">
                    <?php foreach ($libros as $libro): ?>
                        <h3>Género de <?= htmlspecialchars($libro['Genero']) ?></h3>
                        <div class="libro">
                            <div class="seccion-imagen">
                                <img src="../../assets/imgs/menu/<?= htmlspecialchars($libro['Nombre']) ?>.jpg" alt="" class="img-libro">
                            </div>
                            <p class="titulo-libro"><?= htmlspecialchars($libro['Nombre']) ?></p>
                            <ul class="lista-características">
                                <li><b>Autor:</b> <?= htmlspecialchars($libro['Autor']) ?></li>
                                <li><b>Páginas:</b> <?= htmlspecialchars($libro['Paginas']) ?></li>
                                <li><b>Año:</b> <?= htmlspecialchars($libro['Año']) ?></li>
                                <li><b>Género:</b> <?= htmlspecialchars($libro['Genero']) ?></li>
                                <li><b>Editorial:</b> <?= htmlspecialchars($libro['Editorial']) ?></li>
                                <li><b>Ejemplares:</b> <?= htmlspecialchars($libro['Ejemplar']) ?></li>
                            </ul>

                            <div class="sintesis">
                                <p>"<?= htmlspecialchars($libro['Sintesis'] ?? 'Sin sinopsis disponible.') ?>"</p>
                            </div>

                            <div class="buttons">
                                <!-- Formulario para registrar el préstamo -->
                                <form action="libros.php" method="POST">
                                    <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                                    <input type="hidden" name="libro_id" value="<?= $libro['id_Libro'] ?>"> <!-- ID del libro dinámico -->
                                    <input class="btn-solicitar" type="submit" value="Solicitar Préstamo"
                                        onclick="return confirm('¿Estás seguro de que deseas solicitar este libro?')">
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>