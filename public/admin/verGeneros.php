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

// Lógica CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $genero = $_POST['genero'] ?? "";
    $descripcion = $_POST['descripcion'] ?? "";

if ($accion === 'actualizar') { // Crear o actualizar
    if (empty($id)) {
        // Crear nuevo género
        $stmt = $conn->prepare("INSERT INTO tgenero (Nombre, Descripcion) VALUES (:nombre, :descripcion)");
        $stmt->bindParam(':nombre', $genero); // Cambié el bind de 'genero' a 'nombre'
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->execute();
    } else {
        // Actualizar género existente
        $stmt = $conn->prepare("UPDATE tgenero SET Nombre = :nombre, Descripcion = :descripcion WHERE id_Genero = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $genero); // Cambié el bind de 'genero' a 'nombre'
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->execute();
    }

    // Borrar algún género
    } elseif ($accion === 'borrar') { // Eliminar género
        $stmt = $conn->prepare("DELETE FROM tgenero WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}

// Leer géneros de la base de datos
$generos = $conn->query("SELECT id_Genero, Nombre, Descripcion FROM tgenero")->fetchAll(PDO::FETCH_ASSOC);

include '../../templates/a.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Géneros | Admin</title>
    <link rel="stylesheet" href="static/tablasAdmin.css?ver=<?php echo time(); ?>">
</head>

<body>
    <div class="container">
        <h1>Tabla de Géneros</h1>
        <div class="seccion-tabla">

        
            <div class="seccion-buscador">
                <form method="GET" action="verAutores.php">
                    <input type="text" class="buscador" placeholder="Buscar..." name="query"
                        value="<?= htmlspecialchars($queryString) ?>" aria-label="Buscar">
                    <button class="lupa" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <div class="inputs">
                <div class="editors">

                    <h3>Agregar o Editar Género</h3>

                    <form action="verGeneros.php" method="POST">
                        <!-- Campo oculto para el ID (solo en caso de edición) -->
                        <input type="hidden" id="id" name="id" value="<?= htmlspecialchars($id) ?>">

                        <div class="seccion-1">
                            <label for="nombre">Nombre del Género:</label>
                            <input class="input-editor" type="text" id="nombre" name="genero" value="<?= htmlspecialchars($genero) ?>"
                                required><br><br>
                        </div>

                        <div class="seccion-2">
                            <label for="descripcion">Descripción:</label>
                            <textarea class="input-editor" id="descripcion" name="descripcion" rows="4"
                                required><?= htmlspecialchars($descripcion) ?></textarea><br><br>
                        </div>

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
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Mostrar los géneros desde la base de datos -->
                        <?php foreach ($generos as $genero): ?>
                            <tr>
                                <td><?= htmlspecialchars($genero['id_Genero']) ?></td> <!-- Cambiado a id_Genero -->
                                <td><?= htmlspecialchars($genero['Nombre']) ?></td> <!-- Cambiado a Nombre -->
                                <td><?= htmlspecialchars($genero['Descripcion']) ?></td> <!-- Cambiado a Descripcion -->
                                <td>
                                    <!-- Botón Editar -->
                                    <form action="verGeneros.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($genero['id_Genero']) ?>">
                                        <!-- Cambiado a id_Genero -->
                                        <input type="hidden" name="genero" value="<?= htmlspecialchars($genero['Nombre']) ?>">
                                        <!-- Cambiado a Nombre -->
                                        <input type="hidden" name="descripcion" value="<?= htmlspecialchars($genero['Descripcion']) ?>">
                                        <!-- Cambiado a Descripcion -->
                                        <button type="submit" name="accion" value="editar" class="btn-editar">Editar</button>
                                    </form>
                                    <!-- Botón Borrar -->
                                    <form action="verGeneros.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($genero['id_Genero']) ?>">
                                        <!-- Cambiado a id_Genero -->
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