<?php
// Incluir la conexión a la base de datos
include 'db.php';

// Si se envía el formulario para agregar un libro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'];
    $ejemplar = $_POST['ejemplar'];
    $editorial = $_POST['editorial'];
    $genero = $_POST['genero'];
    $paginas = $_POST['paginas'];
    $anio = $_POST['anio'];

    $query = "INSERT INTO tlibros (nombre, ejemplar, editorial, genero, paginas, anio) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$nombre, $ejemplar, $editorial, $genero, $paginas, $anio]);

    header('Location: verLibros.php');
    exit;
}

// Si se envía el formulario para borrar un libro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrar'])) {
    $id = $_POST['id'];
    $query = "DELETE FROM tlibros WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);

    header('Location: verLibros.php');
    exit;
}

// Obtener todos los libros de la base de datos para mostrar en la tabla
$query = "SELECT * FROM tlibros";
$stmt = $pdo->prepare($query);
$stmt->execute();
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ver Libros</title>
    <link rel="stylesheet" href="static/libros.css">
</head>

<body>
    <div class="seccion-tabla">
        <!-- Sección de Inputs para agregar un libro -->
        <div class="seccion1">
            <form action="verLibros.php" method="POST">
                <div class="input-tabla">
                    <label for="nombre">Nombre del libro</label><br>
                    <input type="text" id="nombre" name="nombre" placeholder="Ingresa el nombre" required>

                    <!-- Otros campos necesarios para agregar el libro -->
                    <input type="text" name="ejemplar" placeholder="Ejemplar" required>
                    <input type="text" name="editorial" placeholder="Editorial" required>
                    <input type="text" name="genero" placeholder="Género" required>
                    <input type="number" name="paginas" placeholder="Páginas" required>
                    <input type="number" name="anio" placeholder="Año" required>

                    <button type="submit" name="agregar" class="btn-tabla">Agregar</button>
                </div>
            </form>
        </div>

        <!-- Sección de la tabla -->
        <div class="seccion2">
            <h2>Tabla de Libros</h2>
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
                    <?php foreach ($libros as $libro): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($libro['nombre']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($libro['ejemplar']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($libro['editorial']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($libro['genero']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($libro['paginas']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($libro['anio']); ?>
                            </td>
                            <td>
                                <!-- Botón para borrar -->
                                <form action="verLibros.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $libro['id']; ?>">
                                    <button type="submit" name="borrar" class="btn-tabla">Borrar</button>
                                </form>
                                <!-- Botón para editar (puedes usar JavaScript o PHP para implementar la funcionalidad de edición) -->
                                <form action="editarLibro.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $libro['id']; ?>">
                                    <button type="submit" class="btn-tabla">Editar</button>
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