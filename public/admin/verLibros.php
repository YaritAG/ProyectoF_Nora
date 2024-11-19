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

require_once 'ini.php';

// Verificar si el usuario está logueado (si es necesario para esta página)
verificarSesion();

$libroEdit = null;
$añoActual = date('Y');
$anios = range($añoActual, 1000);

$queryGeneros = "SELECT id_Genero, Nombre FROM tgenero ORDER BY Nombre ASC";
$stmtGeneros = $conn->prepare($queryGeneros);
$stmtGeneros->execute();
$generos = $stmtGeneros->fetchAll(PDO::FETCH_ASSOC);

$queryAutores = "SELECT id_Autor, Nombre FROM tautor ORDER BY Nombre ASC";
$stmtAutores = $conn->prepare($queryAutores);
$stmtAutores->execute();
$autores = $stmtAutores->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'editar') {
    $id = intval($_GET['id'] ?? 0);
    if ($id) {
        $query = "
            SELECT l.id_Libro AS id, l.Nombre AS nombre, l.Ejemplar AS ejemplar,
                   l.Editorial AS editorial, l.Paginas AS paginas, l.Año AS año,
                   l.Sintesis AS sintesis,
                   GROUP_CONCAT(la.TAutor_id_Autor) AS autores,
                   GROUP_CONCAT(lg.TGenero_id_Genero) AS generos
            FROM tlibros l
            LEFT JOIN tautor_has_tlibros la ON l.id_Libro = la.TLibros_id_Libro
            LEFT JOIN tlibros_has_tgenero lg ON l.id_Libro = lg.TLibros_id_Libro
            WHERE l.id_Libro = ?
            GROUP BY l.id_Libro";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        $libroEdit = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($libroEdit) {
            $libroEdit['autor'] = explode(',', $libroEdit['autores'])[0] ?? null;
            $libroEdit['autor_extra'] = explode(',', $libroEdit['autores'])[1] ?? null;
            $libroEdit['generos'] = explode(',', $libroEdit['generos']) ?? [];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id = intval($_POST['id'] ?? 0);

    if ($accion === 'actualizar') {
        $nombre = trim($_POST['nombre']);
        $ejemplar = intval($_POST['ejemplar']);
        $editorial = trim($_POST['editorial']);
        $paginas = intval($_POST['paginas']);
        $año = intval($_POST['año']);
        $sintesis = trim($_POST['sintesis']);
        $generoIds = $_POST['genero'] ?? [];
        $autorPrincipal = $_POST['autor'][0] ?? null;
        $autorSecundario = $_POST['autor'][1] ?? null;

        try {
            $conn->beginTransaction();

            if ($id) {
                $query = "UPDATE tlibros SET Nombre = ?, Ejemplar = ?, Editorial = ?, Paginas = ?, Año = ?, Sintesis = ? WHERE id_Libro = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$nombre, $ejemplar, $editorial, $paginas, $año, $sintesis, $id]);
            } else {
                $query = "INSERT INTO tlibros (Nombre, Ejemplar, Editorial, Paginas, Año, Sintesis) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->execute([$nombre, $ejemplar, $editorial, $paginas, $año, $sintesis]);
                $id = $conn->lastInsertId();
            }

            $query = "DELETE FROM tautor_has_tlibros WHERE TLibros_id_Libro = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id]);

            if ($autorPrincipal) {
                $stmt = $conn->prepare("INSERT INTO tautor_has_tlibros (TLibros_id_Libro, TAutor_id_Autor) VALUES (?, ?)");
                $stmt->execute([$id, $autorPrincipal]);
            }
            if ($autorSecundario) {
                $stmt = $conn->prepare("INSERT INTO tautor_has_tlibros (TLibros_id_Libro, TAutor_id_Autor) VALUES (?, ?)");
                $stmt->execute([$id, $autorSecundario]);
            }

            $query = "DELETE FROM tlibros_has_tgenero WHERE TLibros_id_Libro = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id]);

            foreach ($generoIds as $generoId) {
                $stmt = $conn->prepare("INSERT INTO tlibros_has_tgenero (TLibros_id_Libro, TGenero_id_Genero) VALUES (?, ?)");
                $stmt->execute([$id, $generoId]);
            }

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            $_SESSION['mensaje'] = "Error: " . $e->getMessage();
        }

        header('Location: verLibros.php');
        exit;
    } elseif ($accion === 'borrar') {
        if ($id) {
            $conn->beginTransaction();
            $stmt = $conn->prepare("DELETE FROM tautor_has_tlibros WHERE TLibros_id_Libro = ?");
            $stmt->execute([$id]);

            $stmt = $conn->prepare("DELETE FROM tlibros_has_tgenero WHERE TLibros_id_Libro = ?");
            $stmt->execute([$id]);

            $stmt = $conn->prepare("DELETE FROM tlibros WHERE id_Libro = ?");
            $stmt->execute([$id]);
            $conn->commit();

            $_SESSION['mensaje'] = "Libro eliminado.";
        }
        header('Location: verLibros.php');
        exit;
    }
}

$query = "
    SELECT l.id_Libro AS id, l.Nombre AS nombre, l.Ejemplar AS ejemplar,
           l.Editorial AS editorial, l.Paginas AS paginas, l.Año AS año,
           l.Sintesis AS sintesis,
           GROUP_CONCAT(DISTINCT a.Nombre SEPARATOR ', ') AS autores,
           GROUP_CONCAT(DISTINCT g.Nombre SEPARATOR ', ') AS generos
    FROM tlibros l
    LEFT JOIN tautor_has_tlibros la ON l.id_Libro = la.TLibros_id_Libro
    LEFT JOIN tautor a ON la.TAutor_id_Autor = a.id_Autor
    LEFT JOIN tlibros_has_tgenero lg ON l.id_Libro = lg.TLibros_id_Libro
    LEFT JOIN tgenero g ON lg.TGenero_id_Genero = g.id_Genero
    GROUP BY l.id_Libro
    ORDER BY l.Nombre ASC
";

$stmt = $conn->prepare($query);
$stmt->execute();
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

</head>

<body>
    <div class="container">
        <h1>Tabla de Libros</h1>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="error" id="mensaje-alert">
                <?= htmlspecialchars($_SESSION['mensaje']); ?>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>


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
                                            <option value="<?= $año ?>"
                                        <?= isset($libroEdit['año']) && $libroEdit['año'] == $año ? 'selected' : '' ?>>
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
                                <select id="genero" name="genero[]" class="input-editor" required>
                                    <option value="">Selecciona un género</option>
                                    <?php foreach ($generos as $genero): ?>
                                        <option value="<?= $genero['id_Genero'] ?>" 
                                            <?= isset($libroEdit['generos']) && in_array($genero['id_Genero'], $libroEdit['generos']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($genero['Nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select><br><br>

                                <!-- Número de Páginas -->
                                <label for="paginas">Páginas:</label>
                                <input class="input-editor" type="number" id="paginas" name="paginas"
                                    value="<?= htmlspecialchars($libroEdit['paginas'] ?? '') ?>" required><br><br>
                            </div>

                            <!-- Sintesis del Libro -->                            
                            <label for="sintesis">Síntesis:</label><br><br>
                            <textarea class="input-editor" id="sintesis" name="sintesis" maxlength="255"
                                placeholder="Escribe la síntesis del libro (máx. 255 caracteres)" required><?= htmlspecialchars($libroEdit['sintesis'] ?? '') ?></textarea><br><br>

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
                            <th class="tb-sintesis">Síntesis</th>
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
                                <td><?= htmlspecialchars($libro['sintesis'] ?? 'Sin Síntesis') ?></td>
                                <td>
                                    <!-- Botón de editar -->
                                    <form action="verLibros.php" method="GET" style="display: inline;">
                                        <input type="hidden" name="accion" value="editar">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($libro['id']) ?>">
                                        <button type="submit" class="btn-editar">Editar</button>
                                    </form><br><br>

                                    <!-- Botón de borrar -->
                                    <form action="verLibros.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($libro['id']) ?>">
                                        <button type="submit" name="accion" value="borrar" class="btn-borrar"
                                            onclick="return confirm('¿Estás seguro de que deseas borrar este libro?')">Borrar</button>
                                    </form><br><br>

                                    <!-- Botón de enviar a la sección de libros.php -->
                                    <form action="../user/libros.php" method="POST">
                                        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>"> <!-- Usuario logueado -->
                                        <input type="hidden" name="libro_id" value="<?= $libro['id'] ?>"> <!-- ID del libro -->
                                        <button type="submit" class="btn-enviar" 
                                            onclick="return confirm('¿Estás seguro de que deseas enviar este libro?')">
                                            Enviar Libro
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('#genero').select2({
            placeholder: "Selecciona uno o más géneros"
        });
    });

    // Detectar si la página se recargó con éxito y limpiar el formulario
    window.onload = function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            limpiarFormulario();
        }

        // Ocultar el mensaje de éxito o error después de 5 segundos
        const messages = document.querySelectorAll('.success, .error');
        messages.forEach(message => {
            setTimeout(() => {
                message.style.transition = 'opacity 0.5s';
                message.style.opacity = '0';
                setTimeout(() => message.remove(), 500);
            }, 5000);
        });
        };

         document.addEventListener("DOMContentLoaded", function () {
            const alertBox = document.getElementById('mensaje-alert');
            if (alertBox) {
                // Desvanece el mensaje después de 3 segundos
                setTimeout(() => {
                    alertBox.classList.add('fade-out');
                }, 3000);

                // Elimina el mensaje del DOM después de que se desvanezca
                alertBox.addEventListener('transitionend', () => {
                    alertBox.remove();
                });
            }
        });
    </script>
</body>

</html>