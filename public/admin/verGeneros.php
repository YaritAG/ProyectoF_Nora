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
$id = $genero = $descripcion = "";
$accion = $_POST['accion'] ?? "";
$queryString = $_GET['query'] ?? ""; // Captura el valor del filtro de búsqueda

// Lógica CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $genero = $_POST['genero'] ?? "";
    $descripcion = $_POST['descripcion'] ?? "";

    if ($accion === 'actualizar') { // Crear o actualizar
        if (empty($id)) {
            // Crear nuevo género
            $stmt = $conn->prepare("INSERT INTO tgenero (Nombre, Descripcion) VALUES (:nombre, :descripcion)");
            $stmt->bindParam(':nombre', $genero);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->execute();
        } else {
            // Actualizar género existente
            $stmt = $conn->prepare("UPDATE tgenero SET Nombre = :nombre, Descripcion = :descripcion WHERE id_Genero = :id");
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
$sql = "SELECT id_Genero, Nombre, Descripcion FROM tgenero";
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

                    <form action="verGeneros.php" method="POST">
                        <!-- Campo oculto para almacenar el ID en caso de edición -->
                        <input type="hidden" id="id" name="id" value="<?= htmlspecialchars($id ?? '') ?>">
                    
                        <div class="seccion-1">
                            <!-- Nombre del Género -->
                            <label for="genero">Nombre del Género:</label><br><br>
                            <input type="text" class="input-editor" id="genero" name="genero" value="<?= htmlspecialchars($genero ?? '') ?>" required>
                            <br><br>
                        </div>
                        <div class="seccion-2">
                            <!-- Descripción -->
                            <label for="descripcion">Descripción:</label><br>
                            <textarea id="descripcion" name="descripcion" rows="9" class="input-editor"
                                required><?= htmlspecialchars($descripcion ?? '') ?></textarea>
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
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Mostrar los géneros desde la base de datos -->
                        <?php if (!empty($generos)): ?>
                            <?php foreach ($generos as $genero): ?>
                                <tr>
                                    <td><?= htmlspecialchars($genero['id_Genero']) ?></td>
                                    <td><?= htmlspecialchars($genero['Nombre']) ?></td>
                                    <td><?= htmlspecialchars($genero['Descripcion']) ?></td>
                                    <td>
                                        <!-- Botón Editar -->
                                        <form action="verGeneros.php" method="POST" style="display: inline;">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($genero['id_Genero']) ?>">
                                            <input type="hidden" name="genero" value="<?= htmlspecialchars($genero['Nombre']) ?>">
                                            <input type="hidden" name="descripcion" value="<?= htmlspecialchars($genero['Descripcion']) ?>">
                                            <button type="submit" name="accion" value="editar" class="btn-editar">Editar</button>
                                        </form>
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
                                <td colspan="4">No se encontraron resultados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>