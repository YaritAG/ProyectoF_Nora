<?php
require 'db.php'; // Incluye el archivo de conexión a la base de datos
$conn = getConexion(); // Obtén la instancia de PDO y almacénala en $conn

// Variable para mensajes de error
$error = null;

// Crear o actualizar
if ($accion === 'actualizar') { 
    if (empty($id)) {
        // Crear nuevo género (no incluir id_Genero)
        $stmt = $conn->prepare("INSERT INTO tgenero (Nombre, Descripcion) VALUES (:nombre, :descripcion)");
        $stmt->bindParam(':nombre', $genero); // 'genero' contiene el Nombre
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
}

// Búsqueda de autores
$queryString = isset($_GET['query']) ? $_GET['query'] : '';
$query = $conn->prepare("SELECT * FROM TAutor WHERE Nombre LIKE ?");
$query->execute(["%$queryString%"]);
$autores = $query->fetchAll();

include '../../templates/a.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autores | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="static/tablasAdmin.css?ver=<?php echo time(); ?>">
</head>

<body>
    <div class="container">
        <h1>Tabla de Autores</h1>

        <!-- Mensaje de error -->
        <?php if ($error): ?>
            <div id="error-message" class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="seccion-tabla">
            <div class="seccion-buscador">
                <form method="GET" action="verAutores.php">
                    <input type="text" class="buscador" placeholder="Buscar..." name="query"
                        value="<?= htmlspecialchars($queryString) ?>" aria-label="Buscar">
                    <button class="lupa" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <div class="inputs">
                <div class="editor">
                    <h3>Editar o Agregar Autor</h3>
                    <form action="verGeneros.php" method="POST">
                        <!-- Campo oculto para el ID (solo en caso de edición, se deja vacío para nuevas entradas) -->
                        <input type="hidden" id="id" name="id" value="<?= htmlspecialchars($id ?? '') ?>">
                    
                        <div class="seccion-1">
                            <label for="nombre">Nombre del Género:</label>
                            <input class="input-editor" type="text" id="nombre" name="genero" value="<?= htmlspecialchars($genero ?? '') ?>"
                                required><br><br>
                        </div>
                    
                        <div class="seccion-2">
                            <label for="descripcion">Descripción:</label>
                            <textarea class="input-editor" id="descripcion" name="descripcion" rows="4"
                                required><?= htmlspecialchars($descripcion ?? '') ?></textarea><br><br>
                        </div>
                    
                        <div class="buttons">
                            <button class="btn-save" type="submit" name="accion" value="actualizar">Guardar</button>
                            <button class="btn-cancel" type="reset">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- tabla de Autores -->
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Libros</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($autores as $autor): ?>
                            <tr>
                                <td><?= htmlspecialchars($autor['id_Autor']) ?></td>
                                <td><?= htmlspecialchars($autor['Nombre']) ?></td>
                                <td><?= htmlspecialchars($autor['Libros']) ?></td>
                                <td>
                                    <button
                                        onclick="editarAutor('<?= $autor['id_Autor'] ?>', '<?= htmlspecialchars($autor['Nombre']) ?>', '<?= htmlspecialchars($autor['Libros']) ?>')">
                                        Editar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function editarAutor(id, nombre, libros) {
            // Cargar los datos en los inputs del formulario
            document.getElementById('id').value = id;
            document.getElementById('nombre').value = nombre;
            document.getElementById('libros').value = libros;
        }

        // Ocultar el mensaje de error después de 5 segundos
        window.onload = function () {
            const errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                setTimeout(() => {
                    errorMessage.style.transition = 'opacity 0.5s';
                    errorMessage.style.opacity = '0';
                    setTimeout(() => errorMessage.remove(), 500);
                }, 5000);
            }
        };
    </script>
</body>


</html>