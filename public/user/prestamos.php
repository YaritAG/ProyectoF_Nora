<?php
session_start();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestamos | MyBiblio</title>
    <link rel="stylesheet" href="static/prestamos.css">

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
            <h1>Prestamos</h1>
            <h3>Aquí puedes ver tus libros que tienes en cola y su fecha de préstamo y devolución</h3>
            <h3>¡Recuerda Leer tu libro antes de que acabe el tiempo!</h3>

        </div>
    </div>

</html>