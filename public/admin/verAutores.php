<?php
require 'db.php'; // Incluye el archivo de conexión a la base de datos
$conn = getConexion(); // Obtén la instancia de PDO y almacénala en $conn

// Variable para mensajes de error
$error = null;

// Manejo de acciones de formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'];
    $nombre = $_POST['nombre'];
    $libros = isset($_POST['libros']) ? intval($_POST['libros']) : 0; // Asigna 0 si 'libros' no tiene valor

    if ($accion === 'actualizar') {
        $id = $_POST['id'];

        // Validar si el autor ya existe por ID o nombre
        $queryCheck = $conn->prepare("SELECT * FROM TAutor WHERE id_Autor = ? OR Nombre = ?");
        $queryCheck->execute([$id, $nombre]);
        $autorExistente = $queryCheck->fetch();

        if ($autorExistente) {
            $error = "El autor ya está registrado con el ID o el Nombre proporcionado.";
        } else {
            // Actualizar o insertar si no existe
            if (!empty($id)) {
                $query = $conn->prepare("UPDATE TAutor SET Nombre = ?, Libros = ? WHERE id_Autor = ?");
                $query->execute([$nombre, $libros, $id]);
            } else {
                $query = $conn->prepare("INSERT INTO TAutor (Nombre, Libros) VALUES (?, ?)");
                $query->execute([$nombre, $libros]);
            }
        }
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
                    <form id="editor-form" method="POST" action="verAutores.php">
                        <!-- Campo oculto para almacenar el ID del usuario que se está editando -->
                        <input type="hidden" id="id" name="id">

                        <div class="seccion-2" id="nombreAutor">
                            <label for="nombre">Nombre:</label>
                            <input class="input-editor" type="text" id="nombre" name="nombre" required><br><br>
                        </div>

                        <!-- Contenedor para los botones -->
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