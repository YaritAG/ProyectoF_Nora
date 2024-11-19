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
                
            <?php if (!empty($generos) && is_array($generos)): ?>
                <?php foreach ($generos as $genero): ?>
                    <div class="sec-genero-libro">
                        <a href="genero.php?id=<?= htmlspecialchars($genero['id_Genero']) ?>" class="link-genero">
                            <div class="container-cards">
                                <div class="frente">
                                    <img src="../../<?= htmlspecialchars($genero['Imagen']) ?>"
                                        alt="Imagen de <?= htmlspecialchars($genero['Nombre']) ?>" class="img-genero">
                                    <div class="inner">
                                        <p><?= htmlspecialchars($genero['Nombre']) ?></p>
                                    </div>
                                </div>
                                <div class="detras">
                                    <div class="inner">
                                        <p><?= htmlspecialchars($genero['Descripcion'] ?? 'Sin descripción disponible.') ?></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay géneros disponibles en este momento.</p>
            <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>