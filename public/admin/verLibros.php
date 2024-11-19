<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Validación de sesión
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../../templates/menu.php');
    exit;
}

require 'db.php';
$conn = getConexion();

// Variables iniciales
$libroEdit = null;
$añoActual = date('Y');
$anios = range($añoActual, 1600);

// Obtener géneros
$queryGeneros = "SELECT id_Genero, Nombre FROM tgenero ORDER BY Nombre ASC";
$stmtGeneros = $conn->prepare($queryGeneros);
$stmtGeneros->execute();
$generos = $stmtGeneros->fetchAll(PDO::FETCH_ASSOC);

// Obtener autores
$queryAutores = "SELECT id_Autor, Nombre FROM tautor ORDER BY Nombre ASC";
$stmtAutores = $conn->prepare($queryAutores);
$stmtAutores->execute();
$autores = $stmtAutores->fetchAll(PDO::FETCH_ASSOC);

// Cargar datos del libro para edición
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'editar') {
    $id = intval($_GET['id'] ?? 0);
    if ($id) {
        $query = "
            SELECT l.id_Libro AS id, l.Nombre AS nombre, l.Ejemplar AS ejemplar,
                   l.Editorial AS editorial, l.Paginas AS paginas, l.Año AS año,
                   GROUP_CONCAT(la.TAutor_id_Autor) AS autores
            FROM tlibros l
            LEFT JOIN tautor_has_tlibros la ON l.id_Libro = la.TLibros_id_Libro
            WHERE l.id_Libro = ?
            GROUP BY l.id_Libro";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        $libroEdit = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($libroEdit) {
            $autoresSeleccionados = explode(',', $libroEdit['autores']);
            $libroEdit['autor'] = $autoresSeleccionados[0] ?? null;
            $libroEdit['autor_extra'] = $autoresSeleccionados[1] ?? null;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar') {
    $id = intval($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre']);
    $ejemplar = intval($_POST['ejemplar']);
    $editorial = trim($_POST['editorial']);
    $paginas = intval($_POST['paginas']);
    $año = intval($_POST['año']);
    $autorPrincipal = $_POST['autor'][0] ?? null;
    $autorSecundario = $_POST['autor'][1] ?? null;
    $generoIds = $_POST['genero'] ?? []; // Géneros seleccionados

    if ($id) {
        // Actualizar libro
        $query = "UPDATE tlibros SET Nombre = ?, Ejemplar = ?, Editorial = ?, Paginas = ?, Año = ? WHERE id_Libro = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$nombre, $ejemplar, $editorial, $paginas, $año, $id]);

        // Actualizar autores
        $query = "DELETE FROM tautor_has_tlibros WHERE TLibros_id_Libro = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);

        if ($autorPrincipal) {
            $query = "INSERT INTO tautor_has_tlibros (TLibros_id_Libro, TAutor_id_Autor) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id, $autorPrincipal]);
        }
        if ($autorSecundario) {
            $query = "INSERT INTO tautor_has_tlibros (TLibros_id_Libro, TAutor_id_Autor) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id, $autorSecundario]);
        }

        // Actualizar géneros: Limpiar géneros antiguos y agregar nuevos
        $query = "DELETE FROM tlibros_has_tgenero WHERE TLibros_id_Libro = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);

        foreach ($generoIds as $generoId) {
            $query = "INSERT INTO tlibros_has_tgenero (TLibros_id_Libro, TGenero_id_Genero) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id, $generoId]);
        }
    } else {
        // Insertar nuevo libro
        $query = "INSERT INTO tlibros (Nombre, Ejemplar, Editorial, Paginas, Año) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$nombre, $ejemplar, $editorial, $paginas, $año]);

        $idLibro = $conn->lastInsertId();

        if ($autorPrincipal) {
            $query = "INSERT INTO tautor_has_tlibros (TLibros_id_Libro, TAutor_id_Autor) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$idLibro, $autorPrincipal]);
        }
        if ($autorSecundario) {
            $query = "INSERT INTO tautor_has_tlibros (TLibros_id_Libro, TAutor_id_Autor) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$idLibro, $autorSecundario]);
        }

        foreach ($generoIds as $generoId) {
            $query = "INSERT INTO tlibros_has_tgenero (TLibros_id_Libro, TGenero_id_Genero) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$idLibro, $generoId]);
        }
    }
    header('Location: verLibros.php');
    exit;
}

$query = "
    SELECT l.id_Libro AS id, l.Nombre AS nombre, l.Ejemplar AS ejemplar,
           l.Editorial AS editorial, l.Paginas AS paginas, l.Año AS año,
           GROUP_CONCAT(DISTINCT a.Nombre SEPARATOR ', ') AS autores,
           GROUP_CONCAT(DISTINCT g.Nombre SEPARATOR ', ') AS generos
    FROM tlibros l
    LEFT JOIN tautor_has_tlibros la ON l.id_Libro = la.TLibros_id_Libro
    LEFT JOIN tautor a ON la.TAutor_id_Autor = a.id_Autor
    LEFT JOIN tlibros_has_tgenero lg ON l.id_Libro = lg.TLibros_id_Libro
    LEFT JOIN tgenero g ON lg.TGenero_id_Genero = g.id_Genero
    WHERE 1 = 1
";

if (!empty($_GET['query'])) {
    $query .= " AND (
        l.Nombre LIKE :search OR
        l.Ejemplar LIKE :search OR
        l.Editorial LIKE :search OR
        l.Año LIKE :search OR
        g.Nombre LIKE :search OR
        a.Nombre LIKE :search
    )";
}

$query .= " GROUP BY l.id_Libro ORDER BY l.Nombre ASC";

$stmt = $conn->prepare($query);

if (!empty($_GET['query'])) {
    $searchTerm = '%' . $_GET['query'] . '%';
    $stmt->bindValue(':search', $searchTerm, PDO::PARAM_STR);
}

$stmt->execute();
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Incluir el HTML
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
                <form method="GET" action="verLibros.php">
                    <input type="text" class="buscador" placeholder="Buscar..." name="query" aria-label="Buscar">
                    <button class="lupa" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <!-- Formulario para agregar o editar un libro -->
            <div class="inputs">

                <div class="editors">

                    <h3>Editar o Agregar Libro</h3>

                        <!-- Form para ingresar o editar los datos del libro -->
                        <form id="editor-form" action="verLibros.php" method="POST">
                            <!-- Campo oculto para almacenar el ID en caso de edición -->
                            <input type="hidden" id="id" name="id" value="<?= htmlspecialchars($libroEdit['id'] ?? '') ?>">

                            <div class="seccion-1">
                                <!-- Nombre del Libro -->
                                <label for="nombre">Nombre:</label>
                                <input class="input-editor" type="text" id="nombre" name="nombre"
                                    value="<?= htmlspecialchars($libroEdit['nombre'] ?? '') ?>" required><br><br>

                                <!-- Cantidad de Ejemplares -->
                                <label for="ejemplar">Ejemplares:</label>
                                <input class="input-editor" type="number" id="ejemplar" name="ejemplar"
                                    value="<?= htmlspecialchars($libroEdit['ejemplar'] ?? '') ?>" required><br><br>

                                <!-- Ingreso del año por medio del select y obtiene la condición -->
                                <!-- Año con condición -->
                                <label for="año">Año:</label>
                                <select class="input-editor" id="año" name="año" required>
                                    <option value="">Selecciona un año</option>
                                    <?php foreach ($anios as $año): ?>
                                        <option value="<?= $año ?>" <?= isset($libroEdit['año']) && $libroEdit['año'] == $año ? 'selected' : '' ?>>
                                            <?= $año ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="seccion-2">
                                <!-- Editorial -->
                                <label for="editorial">Editorial:</label>
                                <input class="input-editor" type="text" id="editorial" name="editorial"
                                    value="<?= htmlspecialchars($libroEdit['editorial'] ?? '') ?>" required><br><br>

                                <!-- Género -->
                                <label for="genero">Género:</label>
                                <select id="genero" name="genero" class="input-editor" required>
                                    <option value="">Selecciona un género</option>
                                    <?php foreach ($generos as $genero): ?>
                                        <option value="<?= $genero['id_Genero'] ?>" <?= isset($libroEdit['genero']) && $libroEdit['genero'] == $genero['id_Genero'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($genero['Nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select><br><br>

                                <!-- Número de Páginas -->
                                <label for="paginas">Páginas:</label>
                                <input class="input-editor" type="number" id="paginas" name="paginas"
                                    value="<?= htmlspecialchars($libroEdit['paginas'] ?? '') ?>" required><br><br>
                            </div>

                            <div class="seccion-3">
                                <!-- Autor Principal -->
                                <select class="input-editor" id="autor" name="autor[]" required>
                                    <option value="">Selecciona un autor</option>
                                    <?php foreach ($autores as $autor): ?>
                                        <option value="<?= $autor['id_Autor'] ?>" <?= isset($libroEdit['autor']) && $libroEdit['autor'] == $autor['id_Autor'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($autor['Nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select><br><br>
                                                            
                                <!-- Checkbox para indicar si hay más autores -->
                                <label>
                                    <input type="checkbox" id="mas_autores" name="mas_autores" <?= isset($libroEdit['mas_autores']) && $libroEdit['mas_autores'] ? 'checked' : '' ?> onchange="toggleSegundoAutor()">
                                    Libro con más autores
                                </label><br><br>
                            
                                <!-- Selector para el Segundo Autor -->
                                <div id="segundo_autor" style="<?= isset($libroEdit['autor_extra']) ? '' : 'display: none;' ?>">
                                    <label for="autor_extra">Autor Secundario:</label>
                                    <select class="input-editor" id="autor_extra" name="autor[]">
                                        <option value="">Selecciona un autor</option>
                                        <?php foreach ($autores as $autor): ?>
                                            <option value="<?= $autor['id_Autor'] ?>" <?= isset($libroEdit['autor_extra']) && $libroEdit['autor_extra'] == $autor['id_Autor'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($autor['Nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select><br><br>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="buttons">
                                <!-- Botón para guardar -->
                                <button class="btn-save" type="submit" name="accion" value="actualizar">Guardar</button><br>

                                <!-- Botón para cancelar y limpiar el formulario -->
                                <button type="button" class="btn-cancel" onclick="resetForm('editor-form')">Cancelar</button>
                            </div>
                        </form>
                </div>
            </div>
            
            <!-- script para aparecer la sección del segundo Autor -->
            <script>
                function toggleSegundoAutor() {
                    const checkbox = document.getElementById('mas_autores');
                    const segundoAutorDiv = document.getElementById('segundo_autor');
                    segundoAutorDiv.style.display = checkbox.checked ? 'block' : 'none';
                }

                // Valida el Año 
                document.getElementById('editor-form').addEventListener('submit', function (event) {
                    const año = document.getElementById('año').value;
                    const errorAño = document.getElementById('errorAño');
                    const añoMin = 1600;
                    const añoMax = <?= $añoActual ?>;

                                    if (año < añoMin || año > añoMax) {
                                        event.preventDefault(); // Previene el envío del formulario
                                    errorAño.style.display = 'inline'; // Muestra el mensaje de error
                    } else {
                                        errorAño.style.display = 'none'; // Oculta el mensaje si no hay error
                    }
                });
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
                            <td><?= htmlspecialchars($libro['generos'] ?? 'Sin Género') ?></td>
                            <td><?= htmlspecialchars($libro['paginas'] ?? 'Sin Páginas') ?></td>
                            <td><?= htmlspecialchars($libro['año'] ?? 'Sin Año') ?></td>
                            <td><?= nl2br(htmlspecialchars($libro['autores'] ?? 'Sin Autor')) ?></td>
                            <td>
                                <!-- Botones de acción -->
                                <form action="verLibros.php" method="GET" style="display: inline;">
                                    <input type="hidden" name="accion" value="editar">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($libro['id']) ?>">
                                    <button type="submit" class="btn-editar">Editar</button>
                                </form>

                                <form action="verLibros.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($libro['id']) ?>">
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