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

// Genera un rango de años (desde el actual hacia 1900)
$añoActual = date('Y');
$anios = range($añoActual, 1600);

// Maneja las acciones enviadas por el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // Borrar libro
    if ($accion === 'borrar') {
        $id = $_POST['id'];
        $query = "DELETE FROM tlibros WHERE id_Libro = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);

        // Elimina también la relación con los autores
        $query = "DELETE FROM tautor_has_tlibros WHERE TLibros_id_Libro = ?";
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
                   l.Editorial AS editorial, l.Genero AS genero, 
                   l.Paginas AS paginas, l.Año AS año, ahl.TAutor_id_Autor AS autor
            FROM tlibros l
            LEFT JOIN tautor_has_tlibros ahl ON l.id_Libro = ahl.TLibros_id_Libro
            WHERE l.id_Libro = ?";
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
        $genero = $_POST['genero'];
        $paginas = $_POST['paginas'];
        $año = $_POST['año'];
        $autor = $_POST['autor'];

        // Validación del año
        if ($año > $añoActual || $año < 1900) {
            echo "El año debe ser entre 1900 y $añoActual.";
            exit;
        }

        if (!empty($id)) {
            // Actualiza libro existente
            $query = "UPDATE tlibros SET Nombre = ?, Ejemplar = ?, Editorial = ?, Genero = ?, Paginas = ?, Año = ? WHERE id_Libro = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$nombre, $ejemplar, $editorial, $genero, $paginas, $año, $id]);

            // Actualiza relación autor-libro
            $query = "UPDATE tautor_has_tlibros SET TAutor_id_Autor = ? WHERE TLibros_id_Libro = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$autor, $id]);
        } else {
            // Inserta un nuevo libro
            $query = "INSERT INTO tlibros (Nombre, Ejemplar, Editorial, Genero, Paginas, Año) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$nombre, $ejemplar, $editorial, $genero, $paginas, $año]);

            $idLibro = $conn->lastInsertId();

            // Inserta relación autor-libro
            $query = "INSERT INTO tautor_has_tlibros (TAutor_id_Autor, TLibros_id_Libro) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$autor, $idLibro]);
        }

        header('Location: verLibros.php');
        exit;
    }
}

// Obtener todos los libros para mostrar en la tabla
$query = "
    SELECT l.id_Libro AS id, l.Nombre AS nombre, l.Ejemplar AS ejemplar, 
           l.Editorial AS editorial, l.Genero AS genero, 
           l.Paginas AS paginas, l.Año AS año, a.Nombre AS autor
    FROM tlibros l
    LEFT JOIN tautor_has_tlibros ahl ON l.id_Libro = ahl.TLibros_id_Libro
    LEFT JOIN tautor a ON ahl.TAutor_id_Autor = a.id_Autor
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

// Validación  de datos salientes
echo '<pre>';
print_r($libros);
echo '</pre>';

// Incluir el archivo a.php
include '../../templates/a.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Libros | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="static/tablasAdmin.css">
</head>

<body>
    <div class="container">
        <h1>Tabla de Libros</h1>

        <!-- Formulario para agregar o editar un libro -->
        <div class="inputs">
            <div class="editors">
                <h3>Editar o Agregar Libro</h3>
                <form id="editor-form" action="verLibros.php" method="POST">
                    <!-- Campo oculto para almacenar el ID en caso de edición -->
                    <input type="hidden" id="id" name="id" value="<?= $libroEdit['id_Libro'] ?? '' ?>">

                    <div class="seccion-1">
                        <label for="nombre">Nombre:</label>
                        <input class="input-editor" type="text" id="nombre" name="nombre"

                        value="<?= htmlspecialchars($libroEdit['Nombre'] ?? '') ?>" required><br><br>

                        <label for="ejemplar">Ejemplares:</label>
                        <input class="input-editor" type="number" id="ejemplar" name="ejemplar"
                            value="<?= htmlspecialchars($libroEdit['Ejemplar'] ?? '') ?>" required><br><br>
                    </div>

                    <div class="seccion-2">
                        <label for="editorial">Editorial:</label>
                        <input class="input-editor" type="text" id="editorial" name="editorial"
                            value="<?= htmlspecialchars($libroEdit['Editorial'] ?? '') ?>" required><br><br>

                        <label for="genero">Género:</label>
                        <input class="input-editor" type="text" id="genero" name="genero"
                            value="<?= htmlspecialchars($libroEdit['Genero'] ?? '') ?>" required><br><br>
                        <label for="autor">Autor:</label>

                        <label for="autor">Autor:</label>
                        <select class="input-editor" id="autor" name="autor" required>
                            <option value="">Selecciona un autor</option>
                            <?php foreach ($autores as $autor): ?>
                                <option value="<?= $autor['id_Autor'] ?>" <?= isset($libroEdit['autor']) && $libroEdit['autor'] == $autor['id_Autor'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($autor['Nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    </div>

                    <div class="seccion-3">
                        
                        <label for="paginas">Páginas:</label>
                        <input class="input-editor" type="number" id="paginas" name="paginas"
                            value="<?= htmlspecialchars($libroEdit['Paginas'] ?? '') ?>" required><br><br>

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

                    <!-- Botones -->
                    <div class="buttons">
                        <button class="btn-save" type="submit" name="accion" value="actualizar">Guardar</button>
                        <button class="btn-cancel" type="reset">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de libros -->
        <div class="table-wrapper">
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
                        <th>Autor(es)</th>
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
                            <td><?= htmlspecialchars($libro['autor'] ?? 'Sin Autor') ?></td>
                            <td>
                                <!-- Botón Editar -->
                                <form action="verLibros.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($libro['id'] ?? '') ?>">
                                    <button type="submit" name="accion" value="editar">Editar</button>
                                </form>
                                <!-- Botón Borrar -->
                                <form action="verLibros.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($libro['id'] ?? '') ?>">
                                    <button type="submit" name="accion" value="borrar">Borrar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>