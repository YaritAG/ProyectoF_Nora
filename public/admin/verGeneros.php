<? // Incluir el archivo a.php
include '../../templates/a.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generos | Admin </title>
    <link rel="stylesheet" href="static/tablasAdmin.css?ver=<?php echo time(); ?>">">

</head>
<body>  
    <div class="container">
        <div class="inputs">
            <div class="editors">
                <h3>Agregar Género</h3>
                <form action="verGeneros.php">
                    <!-- Campo oculto para almacenar el ID en caso de edición -->
                    <input type="hidden" id="id" name="id" value="<?= $libroEdit['id_Libro'] ?? '' ?>">

                    <div class="seccion-1">
                        <label for="genero">Género:</label>
                        <input class="input-editor" type="text" id="genero" name="genero" required><br><br>
                    </div>

                    <div seccion-2>
                        <label for="descripcion">descripcion:</label>
                        <!-- Poner un espacio para escribir un texto largo -->
                        <input class="input-editor" type="text" id="genero" name="genero" required><br><br>
                    </div>

                    <!-- Botones -->
                    <div class="buttons">
                        <button class="btn-save" type="submit" name="accion" value="actualizar">Guardar</button>
                        <button class="btn-cancel" type="reset">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Género</th>
                        <th>descripcion</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <form action="verLibros.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($libro['id'] ?? '') ?>">
                                <button type="submit" name="accion" value="editar">Editar</button>
                            </form>
                            <form action="verLibros.php" method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($libro['id'] ?? '') ?>">
                                <button type="submit" name="accion" value="borrar">Borrar</button>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

    </div>
</body>
</html>