<?php 
session_start();

?>

<!DOCTYPE html>
<html lang="es">
<head>
        <meta charset="UTF-8">
        <title>Incio | MiBiblio</title>
        <link rel="stylesheet" href="static/menu.css?ver=<?php echo time(); ?>">

        <!-- Quicksand -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&display=swap" 
        rel="stylesheet">

    </head>
    <body>
        <div class="container">
            <div class="imagen-cabecera">
                <img src="../assets/imgs/menu/destacada3.jpg" alt="Imagen de Cabecera" class="img-cabecera">
            </div>

            <header>
                <?php include 'header.php'; ?>
            </header>
            <!-- Sección donde se encuentra el contenido -->
            <div class="seccion-principal">
                <!-- seccion del titulo -->
                <section class="texto-cabecera fade-in">
                    <!-- Titulo y textos -->
                    <h1 class="titulo1">¡Bienvenido a MyBiblio!</h1>
                    <p class="txt1">
                        Navega a través de las páginas para descubrir todo lo que te ofrece nuestro portal,
                        ¡conoce una nueva manera de leer!
                    </p>       
                </section>

                <!-- Sección de los géneros -->
                <section class="roll-generos fade-in">
                    <div class="text-container">
                        <h1 class="titulo-secciones"><a href="../public/user/generos.php" class="link-generos">Géneros</a> <b>más leídos</b> de nuestro portal</h1>
                        <p class="txt-secciones">
                            Destacan los títulos favoritos de los lectores. Desde misterio hasta romance, aquí encontrarás historias
                            cautivadoras y
                            recomendadas. Sumérgete en libros que han dejado huella y disfruta de una experiencia literaria única y
                            emocionante.
                        </p>
                    </div>
                
                    <!-- Galería de géneros -->
                    <div class="gallery fade-in">
                        <!-- Género Fantasía -->
                        <div class="sec-genero-libro">
                            <a href="/generos/fantasia.html" class="link-genero">
                                <div class="container-cards">
                                    <div class="frente">
                                        <img src="../assets/imgs/menu/fantasia.png" alt="Imagen" class="img-genero">
                                        <div class="inner">
                                            <p>FANTASIA</p>
                                        </div>
                                    </div>
                                    <div class="detras">
                                        <div class="inner">
                                            <p>El género de fantasía explora mundos imaginarios con magia, seres extraordinarios y aventuras épicas, transportando al
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
                                        <img src="../assets/imgs/menu/suspenso.jpg" alt="Imagen" class="img-genero">
                                        <div class="inner">
                                            <p>MISTERIO Y SUSPENSO</p>
                                        </div>
                                    </div>
                                    <div class="detras">
                                        <div class="inner">
                                            <p>El género de misterio y suspenso explora enigmas, crímenes y secretos, manteniendo al lector en tensión constante
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
                                        <img src="../assets/imgs/menu/romantica.jpg" alt="Imagen" class="img-genero">
                                        <div class="inner">
                                            <p>NOVELA ROMÁNTICA</p>
                                        </div>
                                    </div>
                                    <div class="detras">
                                        <div class="inner">
                                            <p>La novela romántica narra historias de amor, enfocándose en la conexión emocional y desafíos entre los protagonistas,
                                            generalmente con finales felices, destacando el poder del amor para superar obstáculos y transformar vidas.</p>
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
                                        <img src="../assets/imgs/menu/cf.jpg" alt="Imagen" class="img-genero">
                                        <div class="inner">
                                            <p>NOVELAS CIENCIA FICCIÓN</p>
                                        </div>
                                    </div>
                                    <div class="detras">
                                        <div class="inner">
                                            <p>El género de ciencia ficción explora mundos futuristas, avances tecnológicos y fenómenos científicos, a menudo con
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
                                        <img src="../assets/imgs/menu/terror.png" alt="Imagen" class="img-genero">
                                        <div class="inner">
                                            <p>TERROR</p>
                                        </div>
                                    </div>
                                    <div class="detras">
                                        <div class="inner">
                                            <p>El género de terror busca provocar miedo y ansiedad en el lector, explorando lo sobrenatural, lo
                                                macabro y situaciones
                                                inquietantes.</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </section>

                <!----------------------------------->
                <!-- Sección de Libros más famosos -->
                <!----------------------------------->
                <section class="roll-librostop fade-in">
                    <div class="text-container">
                        <h1 class="titulo-secciones">Libros <b>más leídos</b> de la semana</h1>
                        <p class="txt-secciones">
                            Destacan los títulos favoritos de los lectores. Desde misterio hasta romance, aquí encontrarás historias
                            cautivadoras y
                            recomendadas. Sumérgete en libros que han dejado huella y disfruta de una experiencia literaria única y
                            emocionante.
                        </p>
                    </div>

                    <!-- Libro de Mago de OZ -->
                    <div class="gallery fade-in">
                        <div class="seccion-libro">
                            <img src="../assets/imgs/menu/magodeoz.jpg" alt="Portada" class="foto-portada">
                            <div class="card">
                                <div class="content">
                                    <h1>El Maravilloso Mago de OZ</h1>
                                    <p><b>Autor:</b> L. Frank Baum</p>
                                    <p><b>Género: </b>Fantasía</p>
                                    <p><b>Páginas: </b>256</p>
                                    <button class="btn">Ver Libro</button>
                                </div>
                            </div>
                        </div>

                        <!-- Libro de Boulevard-->
                        <div class="seccion-libro">
                            <img src="../assets/imgs/menu/boulevard.jpg" alt="Portada" class="foto-portada">
                            <div class="card">
                                <div class="content">
                                    <h1>Boulevard</h1>
                                    <p><b>Autor:</b> Flor M. Salvador</p>
                                    <p><b>Género: </b>Novela Ciencia Ficción</p>
                                    <p><b>Páginas: </b>360</p>
                                    <button class="btn">Ver Libro</button>
                                </div>
                            </div>
                        </div>

                        <!-- Libro de Don Quijote -->
                        <div class="seccion-libro">
                            <img src="../assets/imgs/menu/donquijote.jpg" alt="Portada" class="foto-portada">
                            <div class="card">
                                <div class="content">
                                    <h1>Don Quijote de la Mancha</h1>
                                    <p><b>Autor:</b> Miguel de Cervantes</p>
                                    <p><b>Género: </b>Novela Ciencia Ficción</p>
                                    <p><b>Páginas: </b>1056</p>
                                    <button class="btn">Ver Libro</button>
                                </div>
                            </div>
                        </div>

                        <!-- Libro de IT -->
                        <div class="seccion-libro">
                            <img src="../assets/imgs/menu/it.jpg" alt="Portada" class="foto-portada">
                            <div class="card">
                                <div class="content">
                                    <h1>IT (ESO)</h1>
                                    <p><b>Autor:</b> Stephen King</p>
                                    <p><b>Género: </b>Terror</p>
                                    <p><b>Páginas: </b>1504</p>
                                    <button class="btn">Ver Libro</button>
                                </div>
                            </div>
                        </div>

                        <!-- Libro del amor en los tiempos del colera -->
                        <div class="seccion-libro">
                            <img src="../assets/imgs/menu/elamor.jpg" alt="Portada" class="foto-portada">
                            <div class="card">
                                <div class="content">
                                    <h1>EL Amor en los Tiempos del Cólera</h1>
                                    <p><b>Autor:</b> Gabriel García Márquez</p>
                                    <p><b>Género: </b>Novela Romántica</p>
                                    <p><b>Páginas: </b>496</p>
                                    <button class="btn">Ver Libro</button>
                                </div>
                            </div>
                        </div>

                        <!-- Libro de Pedro Páramo-->
                        <div class="seccion-libro">
                            <img src="../assets/imgs/menu/pp.jpg" alt="Portada" class="foto-portada">
                            <div class="card">
                                <div class="content">
                                    <h1>Pedro Páramo</h1>
                                    <p><b>Autor:</b> Gabriel García Márquez</p>
                                    <p><b>Género: </b>Fantasía</p>
                                    <p><b>Páginas: </b>122</p>
                                    <button class="btn">Ver Libro</button>
                                </div>
                            </div>
                        </div>

                        <!-- Odisea -->
                        <div class="seccion-libro">
                            <img src="../assets/imgs/menu/odisea.jpg" alt="Portada" class="foto-portada">
                            <div class="card">
                                <div class="content">
                                    <h1>Odisea</h1>
                                    <p><b>Autor:</b> Homero</p>
                                    <p><b>Género: </b>Fantasía</p>
                                    <p><b>Páginas: </b>448</p>
                                    <button class="btn">Ver Libro</button>
                                </div>
                            </div>
                        </div>

                        <!-- Libro Dune -->
                        <div class="seccion-libro">
                            <img src="../assets/imgs/menu/dune.jpg" alt="Portada" class="foto-portada">
                            <div class="card">
                                <div class="content">
                                    <h1>Dune</h1>
                                    <p><b>Autor:</b> Frank Herbert</p>
                                    <p><b>Género: </b>Novela Ciencia Ficción</p>
                                    <p><b>Páginas: </b>784</p>
                                    <button class="btn">Ver Libro</button>
                                </div>
                            </div>
                        </div>

                        <script>
                            // Script para ajustar el texto dependiendi de la cantidad de texto en el contenido
                            document.querySelectorAll('.seccion-libro').forEach(libro => {
                                    const content = libro.querySelector('.card .content');
                                    const sectionHeight = libro.clientHeight;

                                    libro.addEventListener('mouseover', () => {
                                        const contentHeight = content.clientHeight;

                                        // Calcula cuánto debe levantarse el contenido en función de su altura
                                        const newTop = sectionHeight - contentHeight - 10; // 10px desde el fondo
                                        content.style.top = `${newTop}px`;
                                    });

                                    libro.addEventListener('mouseleave', () => {
                                        // Restaura la posición original cuando el mouse sale
                                        content.style.top = '180px';
                                    });
                                });
                        </script>
                    </div>
                </section>

                <section class="info">
                    <div class="seccion">
                        <h1 class="titulo-secciones fade-in" id="tittle-info">¿Por qué es importante leer?</h1>
                        <p class="txt-secciones fade-in" id="txt-info1">
                            Leer es una actividad enriquecedora y transformadora que va mucho más allá de una simple distracción. A través de la
                            lectura, las personas no solo adquieren conocimientos, sino que también desarrollan habilidades cruciales para su vida
                            cotidiana y profesional.
                        </p><br>
                        <p class="txt-secciones fade-in" id="txt-info2">
                            Beneficios de la Lectura: 
                            <ul class="txt-secciones">
                                <br><li class="li-1 fade-in"><b>Expande el Conocimiento:</b> Cada libro es una fuente de información que puede ampliar nuestra comprensión sobre temas
                                específicos y enseñarnos sobre culturas, ciencias, historia, y mucho más.
                                <img src="" alt="" class="img-li-1"></li><br>

                                <li class="li-2 fade-in"><b>Mejora la Concentración y la Memoria:</b> Leer con frecuencia ayuda a mantener la mente activa, fortaleciendo la memoria y
                                la concentración, dos habilidades fundamentales en la era digital.
                                <img src="" alt="" class="img-li-2"></li><br>

                                <li class="li-1 fade-in"><b>Desarrolla el Vocabulario y la Expresión:</b> Al leer, encontramos palabras y expresiones nuevas que enriquecen nuestro
                                vocabulario, ayudándonos a expresar ideas y pensamientos de manera más clara y efectiva.
                                <img src="" alt="" class="img-li-1"></li><br>

                                <li class="li-2 fade-in"><b>Estimula la Imaginación y Creatividad:</b> La lectura permite crear mundos en nuestra mente, lo que fomenta la creatividad y
                                ayuda a encontrar inspiración.
                                <img src="" alt="" class="img-li-2"></li><br>

                                <li class="li-1 fade-in"><b>Reduce el Estrés:</b> Sumergirse en una buena historia ayuda a desconectar de las preocupaciones diarias, promoviendo la
                                relajación y el bienestar emocional.
                                <img src="" alt="" class="img-li1"></li>
                            </ul>                        
                        </p>
                    </div>
                </section>
            </div>
        </div>
        <script>
        // Seleccionamos todas las secciones a las que queremos aplicar el efecto
        const sections = document.querySelectorAll('.fade-in');

        // Configuramos el IntersectionObserver para activar o desactivar el efecto
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible'); // Añade la clase cuando entra en la vista
                } else {
                    entry.target.classList.remove('visible'); // Elimina la clase cuando sale de la vista
                }
            });
        }, {
            threshold: 0.1 // Configuración para activar el efecto cuando el 10% de la sección esté en la vista
        });

        // Observamos cada sección
        sections.forEach(section => {
            observer.observe(section);
        });
    </script>

    </body>
</html>