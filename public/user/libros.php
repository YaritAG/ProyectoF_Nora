<?php
session_start();

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
                            <form action="libros.php" method="POST">
                                <input type="sumbit" value="Solicitar Préstamo">
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

        </div>
    </div>
</html>