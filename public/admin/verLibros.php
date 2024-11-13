<?php

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Ver Libros</title>
</head>
<body>
    <div class="container">
        <div class="seccion-tabla">

            <!-- Seccion de Inputs -->
            <div class="seccion1">
                <form action="verLibros.php" method="POST">
                    <div class="input-tabla">
                        <label for="nombre">Nombre del libros</label><br>
                        <input type="text" id="nombre" name="nombre" placeholder="Ingresa tu Nombre" required>
                        <i class="icon user"></i>
                    </div>
                </form>
            </div>

            <!-- Seccion de la tabla -->
            <div class="seccion2">
                <h2>Tabla de Libros</h2>

                <!-- Tabla de Libros CRUD de libros -->
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Ejemplar</th>
                            <th>Editorial</th>
                            <th>Género</th>
                            <th>Páginas</th>
                            <th>Año</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>
                                <button type="submit" class="btn-tabla">Borrar</button>
                                <button type="sumbit" class="btn-tabla">Editar</button>
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>