<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Verifica si el usuario está logueado y tiene rol de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../../templates/menu.php'); // Redirige si no es administrador
    exit;
}

require 'db.php'; // Archivo de conexión a la base de datos
$conn = getConexion();

// Inicialización de variables
$id = $genero = $descripcion = $imagenRuta = "";
$accion = $_POST['accion'] ?? "";
$queryString = $_GET['query'] ?? ""; // Captura el valor del filtro de búsqueda

// Lógica CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $genero = $_POST['genero'] ?? "";
    $descripcion = $_POST['descripcion'] ?? "";
    $imagenRuta = null;

    // Manejar la imagen subida
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $directorioSubida = '../../assets/imgs/generos/'; // Ruta relativa a la carpeta de destino
        $nombreArchivo = uniqid() . '_' . basename($_FILES['imagen']['name']); // Genera un nombre único para la imagen
        $rutaCompleta = $directorioSubida . $nombreArchivo;

        // Mueve la imagen subida al directorio de destino
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaCompleta)) {
            $imagenRuta = 'assets/imgs/generos/' . $nombreArchivo; // Ruta relativa para guardar en la base de datos
        } else {
            echo "Error al subir la imagen.";
        }
    }

    if ($accion === 'actualizar') { // Crear o actualizar
        if (empty($id)) {
            // Crear nuevo género
            $stmt = $conn->prepare("INSERT INTO tgenero (Nombre, Descripcion, Imagen) VALUES (:nombre, :descripcion, :imagen)");
            $stmt->bindParam(':nombre', $genero);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':imagen', $imagenRuta);
            $stmt->execute();
        } else {
            // Actualizar género existente
            if ($imagenRuta) {
                // Actualiza también la imagen si se subió una nueva
                $stmt = $conn->prepare("UPDATE tgenero SET Nombre = :nombre, Descripcion = :descripcion, Imagen = :imagen WHERE id_Genero = :id");
                $stmt->bindParam(':imagen', $imagenRuta);
            } else {
                // No actualiza la imagen si no se subió una nueva
                $stmt = $conn->prepare("UPDATE tgenero SET Nombre = :nombre, Descripcion = :descripcion WHERE id_Genero = :id");
            }
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nombre', $genero);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->execute();
        }
    } elseif ($accion === 'borrar') { // Eliminar género
        $stmt = $conn->prepare("DELETE FROM tgenero WHERE id_Genero = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    // Redirige a la misma página después de procesar la acción
    header('Location: verGeneros.php');
    exit;
}

// Leer géneros de la base de datos con filtro
$sql = "SELECT id_Genero, Nombre, Descripcion, Imagen FROM tgenero";
$params = [];

// Aplica el filtro si se proporciona un query
if (!empty($queryString)) {
    $sql .= " WHERE id_Genero LIKE ? OR Nombre LIKE ?";
    $params = ["%$queryString%", "%$queryString%"];
}

$sql .= " ORDER BY Nombre ASC"; // Ordena alfabéticamente
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$generos = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../../templates/a.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Géneros | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="static/tablasAdmin.css?ver=<?php echo time(); ?>"></head>

<body>
    <div class="container">
        <h1>Tabla de Géneros</h1>
        <div class="seccion-tabla">

            <!-- Buscador -->
            <div class="seccion-buscador">
                <form method="GET" action="verGeneros.php">
                    <input type="text" class="buscador" placeholder="Buscar por ID o Nombre..." name="query"
                        value="<?= isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '' ?>" aria-label="Buscar">
                    <button class="lupa" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <div class="inputs">
                <div class="editors">

                    <h3>Agregar o Editar Género</h3>

                    <form action="verGeneros.php" method="POST" enctype="multipart/form-data">
                        <!-- Campo oculto para almacenar el ID en caso de edición -->
                        <input type="hidden" id="id" name="id" value="<?= htmlspecialchars($id ?? '') ?>">

                        <div class="seccion-1">
                            <!-- Nombre del Género -->
                            <label for="genero">Nombre del Género:</label><br><br>
                            <input type="text" class="input-editor" id="genero" name="genero" value="<?= htmlspecialchars($genero ?? '') ?>"
                                required>
                            <br><br>
                        </div>

                        <div class="seccion-2">
                            <!-- Descripción -->
                            <label for="descripcion">Descripción:</label><br>
                            <textarea id="descripcion" name="descripcion" rows="9" class="input-editor"
                                required><?= htmlspecialchars($descripcion ?? '') ?></textarea>
                            <br><br>
                        </div>

                        <div class="seccion-3">
                            <!-- Subir Imagen -->
                            <label for="imagen">Seleccionar Imagen:</label><br><br>
                            <label for="imagen" id="img-advert">*Recuerda que la imagen tiene que ser cuadrada porque su medida
                                será de 300px x 300px, así que si no es cuadrada se verá afectada :(*
                            </label><br><br>
                            <input type="file" id="imagen" name="imagen" accept="image/*">
                            <br><br>
                        </div>

                        <!-- Botones -->
                        <button class="btn-save" type="submit" name="accion" value="actualizar">Guardar</button>
                        <button class="btn-cancel" type="reset">Cancelar</button>
                    </form>
                </div>
            </div>

            <!-- Tabla de contenido  -->
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Género</th>
                            <th>Descripción</th>
                            <th>Imagen</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($generos)): ?>
                            <?php foreach ($generos as $genero): ?>
                                <tr>
                                    <td><?= htmlspecialchars($genero['id_Genero']) ?></td>
                                    <td><?= htmlspecialchars($genero['Nombre']) ?></td>
                                    <td><?= htmlspecialchars($genero['Descripcion']) ?></td>
                                    <td>
                                        <?php if (!empty($genero['Imagen'])): ?>
                                            <img src="<?= htmlspecialchars($genero['Imagen']) ?>" alt="Imagen del género"
                                                style="width: 50px; height: 50px; border-radius: 5px;">
                                            <?php else: ?>
                                                <span>No Imagen</span>
                                            <?php endif; ?>
                                    </td>
                                    <td>
                                        <!-- Botón Editar -->
                                        <form action="verGeneros.php" method="POST" style="display: inline;">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($genero['id_Genero']) ?>">
                                            <input type="hidden" name="genero" value="<?= htmlspecialchars($genero['Nombre']) ?>">
                                            <input type="hidden" name="descripcion" value="<?= htmlspecialchars($genero['Descripcion']) ?>">
                                            <button type="submit" name="accion" value="editar" class="btn-editar">Editar</button><br>
                                        </form><br>
                                        <!-- Botón Borrar -->
                                        <form action="verGeneros.php" method="POST" style="display: inline;">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($genero['id_Genero']) ?>">
                                            <button type="submit" name="accion" value="borrar" class="btn-borrar">Borrar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No se encontraron resultados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>