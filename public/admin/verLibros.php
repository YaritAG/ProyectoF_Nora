<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../../templates/menu.php');
    exit;
}

require 'db.php';
$conn = getConexion();

// Variable para prellenar el formulario en caso de edición
$libroEdit = null;

// Genera un rango de años (desde el actual hacia 1600)
$añoActual = date('Y');
$anios = range($añoActual, 1600);

// Obtener los géneros para el select
$queryGeneros = "SELECT id_Genero, Nombre FROM tgenero ORDER BY Nombre ASC";
$stmtGeneros = $conn->prepare($queryGeneros);
$stmtGeneros->execute();
$generos = $stmtGeneros->fetchAll(PDO::FETCH_ASSOC);

// Maneja las acciones enviadas por el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // Borrar libro
    if ($accion === 'borrar') {
        $id = $_POST['id'];
        $query = "DELETE FROM tlibros WHERE id_Libro = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);

        // Elimina también la relación con géneros
        $query = "DELETE FROM tlibros_has_tgenero WHERE TLibros_id_Libro = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);

        header('Location: verLibros.php');
        exit;
    }

    // Editar libro (prellenar formulario)
    if ($accion === 'editar') {
        $id = $_POST['id'];
        $query = "
            SELECT l.id_Libro AS id, l.Nombre AS nombre, l.Ejemplar AS ejemplar,
                   l.Editorial AS editorial, l.Paginas AS paginas, l.Año AS año, 
                   g.id_Genero AS genero
            FROM tlibros l
            LEFT JOIN tlibros_has_tgenero lhg ON l.id_Libro = lhg.TLibros_id_Libro
            LEFT JOIN tgenero g ON lhg.TGenero_id_Genero = g.id_Genero
            WHERE l.id_Libro = ?
            LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        $libroEdit = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar o insertar libro
    if ($accion === 'actualizar') {
        $id = $_POST['id'] ?? '';
        $nombre = $_POST['nombre'];
        $ejemplar = $_POST['ejemplar'];
        $editorial = $_POST['editorial'];
        $paginas = $_POST['paginas'];
        $año = $_POST['año'];
        $generoSeleccionado = $_POST['genero'] ?? null; // Género seleccionado del select

        // Validación del año
        if ($año > $añoActual || $año < 1600) {
            echo "El año debe ser entre 1600 y $añoActual.";
            exit;
        }

        if (!empty($id)) {
            // Actualiza libro existente
            $query = "UPDATE tlibros SET Nombre = ?, Ejemplar = ?, Editorial = ?, Paginas = ?, Año = ? WHERE id_Libro = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$nombre, $ejemplar, $editorial, $paginas, $año, $id]);

            // Actualiza la relación de género (solo si no existe ya)
            if ($generoSeleccionado) {
                $query = "SELECT COUNT(*) FROM tlibros_has_tgenero WHERE TLibros_id_Libro = ? AND TGenero_id_Genero = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$id, $generoSeleccionado]);
                $existe = $stmt->fetchColumn();

                if (!$existe) {
                    // Insertar la relación si no existe
                    $query = "INSERT INTO tlibros_has_tgenero (TLibros_id_Libro, TGenero_id_Genero) VALUES (?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$id, $generoSeleccionado]);
                }
            }
        } else {
            // Inserta un nuevo libro
            $query = "INSERT INTO tlibros (Nombre, Ejemplar, Editorial, Paginas, Año) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$nombre, $ejemplar, $editorial, $paginas, $año]);

            $idLibro = $conn->lastInsertId();

            // Inserta la relación de género
            if ($generoSeleccionado) {
                $query = "INSERT INTO tlibros_has_tgenero (TLibros_id_Libro, TGenero_id_Genero) VALUES (?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->execute([$idLibro, $generoSeleccionado]);
            }
        }

        header('Location: verLibros.php');
        exit;
    }
}

// Obtener todos los libros para mostrar en la tabla
$query = "
    SELECT l.id_Libro AS id, l.Nombre AS nombre, l.Ejemplar AS ejemplar, 
           l.Editorial AS editorial, l.Paginas AS paginas, l.Año AS año,
           g.Nombre AS genero
    FROM tlibros l
    LEFT JOIN tlibros_has_tgenero lhg ON l.id_Libro = lhg.TLibros_id_Libro
    LEFT JOIN tgenero g ON lhg.TGenero_id_Genero = g.id_Genero
    ORDER BY l.Nombre ASC;
";
$stmt = $conn->prepare($query);
$stmt->execute();
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener autores para el selector
$queryAutores = "SELECT id_Autor, Nombre FROM tautor ORDER BY Nombre ASC";
$stmtAutores = $conn->prepare($queryAutores);
$stmtAutores->execute();
$autores = $stmtAutores->fetchAll(PDO::FETCH_ASSOC);

// Incluir el archivo a.php
include '../../templates/a.php';
?>

<!-- Archivo HTML -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Libros | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="static/tablasAdmin.css?=ver<?php echo time(); ?>">
</head>

<body>
    <div class="container">
        <h1>Tabla de Libros</h1>

        <div class="seccion-tabla">
                      
            <!-- Buscador con método GET para obtener datos -->    
            <div class="seccion-buscador">
                <form method="GET" action="verUsuarios.php">
                    <input type="text" class="buscador" placeholder="Buscar..." name="query" aria-label="Buscar">
                    <button class="lupa" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <!-- Formulario para agregar o editar un libro -->
            <div class="inputs">

                <div class="editors">

                    <h3>Editar o Agregar Libro</h3>

                    <!-- Form para ingresar los datos -->
                    <form id="editor-form" action="verLibros.php" method="POST">
                    
                    <!-- Campo oculto para almacenar el ID en caso de edición -->
                    <input type="hidden" id="id" name="id" value="<?= $libroEdit['id_Libro'] ?? '' ?>">   
                    
                        <div class="seccion-1">

                            <!-- Nombre del Libro -->
                            <label for="nombre">Nombre:</label>
                            <input class="input-editor" type="text" id="nombre" name="nombre"
                                value="<?= htmlspecialchars($libroEdit['Nombre'] ?? '') ?>" required><br><br>
                    
                            <!-- Cantidad de Ejemplares -->
                            <label for="ejemplar">Ejemplares:</label>
                            <input class="input-editor" type="number" id="ejemplar" name="ejemplar"
                                value="<?= htmlspecialchars($libroEdit['Ejemplar'] ?? '') ?>" required><br><br>
                                        
                            <!-- Ingreso del año por medio del select y obtiene la condición -->
                            <label for="año">Año:</label>
                            <select class="input-editor" id="año" name="año" required>
                                <option value="">Selecciona un año</option>
                                <?php foreach ($anios as $año): ?>
                                    <option value="<?= $año ?>" <?= isset($libroEdit['Año']) && $libroEdit['Año'] == $año ? 'selected' : '' ?>>
                                        <?= $año ?>
                                    </option>
                                <?php endforeach; ?>
                            </select><br><br>
                        </div>
                    
                        <div class="seccion-2">

                            <!-- Ingresar el Editoria -->
                            <label for="editorial">Editorial:</label>
                            <input class="input-editor" type="text" id="editorial" name="editorial"
                                value="<?= htmlspecialchars($libroEdit['Editorial'] ?? '') ?>" required><br><br>
                            
                            <!-- Seleccionar Género -->
                            <label for="genero">Género:</label>
                            <select id="genero" name="genero" class="input-editor" required>
                                <option value="">Selecciona un género</option>
                                <?php foreach ($generos as $genero): ?>
                                    <option value="<?= $genero['id_Genero'] ?>" <?= isset($libroEdit['generos']) && in_array($genero['id_Genero'], explode(',', $libroEdit['generos'])) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($genero['Nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <br><br>

                            <!-- Ingresar el numero de páginas del libro -->
                            <label for="paginas">Páginas:</label>     
                            <input class="input-editor" type="number" id="paginas" name="paginas"
                                value="<?= htmlspecialchars($libroEdit['Paginas'] ?? '') ?>" required><br><br>
                        </div>
                    
                        <div class="seccion-3">
                                
                            <!-- Autor Principal -->
                            <label for="autor">Autor Principal:</label>
                            <select class="input-editor" id="autor" name="autor[]" required>
                                <option value="">Selecciona un autor</option>
                                <?php foreach ($autores as $autor): ?>
                                    <option value="<?= $autor['id_Autor'] ?>" <?= isset($libroEdit['autores']) && in_array($autor['id_Autor'], explode(',', $libroEdit['autores'])) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($autor['Nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select><br><br>
                                    
                            <!-- Checbox para validar si hay mas autores -->
                            <label>
                                <input type="checkbox" id="mas_autores" name="mas_autores" onchange="toggleSegundoAutor()"> Libro con más
                                autores
                            </label><br><br>
                                
                            <!-- Input para el segundo autor -->
                            <div id="segundo_autor" style="display: none;">
                                <label for="autor_extra">Autor Secundario:</label>
                                <select class="input-editor" id="autor_extra" name="autor[]">
                                    <option value="">Selecciona un autor</option>
                                    <?php foreach ($autores as $autor): ?>
                                        <option value="<?= $autor['id_Autor'] ?>"><?= htmlspecialchars($autor['Nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select><br><br>
                            </div>
                        </div>
                    
                        <!-- Botones -->
                        <div class="buttons">
                            <button class="btn-save" type="submit" name="accion" value="actualizar">Guardar</button>
                            <button class="btn-cancel" type="reset">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- script para aparecer la sección del segundo Autor -->
            <script>
                // Muestra/oculta el segundo autor
                function toggleSegundoAutor() {
                    const checkbox = document.getElementById('mas_autores');
                    const segundoAutor = document.getElementById('segundo_autor');
                    segundoAutor.style.display = checkbox.checked ? 'block' : 'none';
                }
            </script>
            
            <!-- Tabla de libros -->
            <div class="table-wrapper" id="tabla-libros">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Ejemplares</th>
                            <th>Editorial</th>
                            <th>Género</th>
                            <th>Páginas</th>
                            <th>Año</th>
                            <th>Autores</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php foreach ($libros as $libro): ?>
                                        <tr>
                                <td><?= htmlspecialchars($libro['id'] ?? 'Sin ID') ?></td>
                                <td><?= htmlspecialchars($libro['nombre'] ?? 'Sin Nombre') ?></td>
                                <td><?= htmlspecialchars($libro['ejemplar'] ?? 'Sin Ejemplares') ?></td>
                                <td><?= htmlspecialchars($libro['editorial'] ?? 'Sin Editorial') ?></td>
                                <td><?= htmlspecialchars($libro['genero'] ?? 'Sin Género') ?></td>
                                <td><?= htmlspecialchars($libro['paginas'] ?? 'Sin Páginas') ?></td>
                                <td><?= htmlspecialchars($libro['año'] ?? 'Sin Año') ?></td>
                                <td><?= nl2br(htmlspecialchars($libro['autores'] ?? 'Sin Autor')) ?></td>
                                <td>
                                    <form action="verLibros.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($libro['id'] ?? '') ?>">
                                        <button type="submit" name="accion" value="editar" class="btn-editar">Editar</button>
                                    </form>
                                    <form action="verLibros.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($libro['id'] ?? '') ?>">
                                        <button type="submit" name="accion" value="borrar" class="btn-borrar">Borrar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>