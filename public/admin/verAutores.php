<?php
require 'db.php'; // Archivo de conexión a la base de datos
$conn = getConexion(); // Obtener la conexión PDO

require_once 'ini.php';

// Verificar si el usuario está logueado (si es necesario para esta página)
verificarSesion();

// Variable para mensajes
$error = null;
$success = null;

try {
    // Crear, actualizar o borrar autor
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $accion = $_POST['accion'] ?? '';
        $id = intval($_POST['id'] ?? 0);

        if ($accion === 'actualizar') {
            $nombre = trim($_POST['nombre']);
            if ($id === 0) {
                // Crear nuevo autor
                $stmt = $conn->prepare("INSERT INTO tautor (Nombre) VALUES (:nombre)");
                $stmt->bindParam(':nombre', $nombre);
                $stmt->execute();
                $success = "Autor Registrado";
            } else {
                // Actualizar autor existente
                $stmt = $conn->prepare("UPDATE tautor SET Nombre = :nombre WHERE id_Autor = :id");
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->execute();
                $success = "Autor Actualizado";
            }
            // Redirigir para limpiar los campos
            header("Location: verAutores.php?success=" . urlencode($success));
            exit;
        } elseif ($accion === 'borrar' && $id > 0) {
            try {
                // Intentar eliminar el autor
                $stmt = $conn->prepare("DELETE FROM tautor WHERE id_Autor = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $success = "Autor Borrado";
            } catch (PDOException $e) {
                // Si ocurre un error de clave foránea, mostrar un mensaje
                if ($e->getCode() === '23000') {
                    $error = "No se puede eliminar el autor porque tiene libros asociados.";
                } else {
                    throw $e; // Re-lanzar otros errores
                }
            }
        }
    }

    // Búsqueda de autores con el número de libros calculado
    $queryString = $_GET['query'] ?? ''; // Buscar por nombre
    $query = $conn->prepare("
        SELECT a.id_Autor, a.Nombre,
               (SELECT COUNT(*) 
                FROM tautor_has_tlibros th
                WHERE th.TAutor_id_Autor = a.id_Autor) AS Libros
        FROM tautor a
        WHERE a.Nombre LIKE ?
        ORDER BY a.Nombre ASC
    ");
    $query->execute(["%$queryString%"]);
    $autores = $query->fetchAll(); // Obtener todos los resultados

    // Capturar mensajes de éxito desde la redirección
    $success = $_GET['success'] ?? $success;
} catch (PDOException $e) {
    $error = "Error en la base de datos: " . $e->getMessage();
}

include '../../templates/a.php'; // Incluir el archivo HTML
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

        
        <!-- Mensajes de éxito -->
        <?php if ($success): ?>
            <div class="success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- Mensaje de error -->
        <?php if ($error): ?>
            <div id="error-message" class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="seccion-tabla">
            <!-- Buscador -->
            <div class="seccion-buscador">
                <form method="GET" action="verAutores.php">
                    <input type="text" class="buscador" placeholder="Buscar por nombre..." name="query"
                        value="<?= htmlspecialchars($queryString) ?>" aria-label="Buscar">
                    <button class="lupa" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <!-- Formulario para editar o agregar autor -->
            <div class="inputs">
                <div class="editor">
                    <h3>Editar o Agregar Autor</h3>
                    <form action="verAutores.php" method="POST" id="autor-form">
    <input type="hidden" id="id" name="id" value="<?= htmlspecialchars($id ?? '') ?>">
                    
                        <!-- Nombre del autor -->
                        <label for="nombre">Nombre del Autor:</label>
                        <input class="input-editor" type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($nombre ?? '') ?>"
                            required><br><br>
                    
                        <!-- Botones -->
                        <div class="buttons">
                            <button class="btn-save" type="submit" name="accion" value="actualizar">Guardar</button>
                            <button class="btn-cancel" type="reset">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Autores -->
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Número de Libros</th>
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
                                    <!-- Botón Editar -->
                                    <button class="btn-editar"
                                        onclick="editarAutor('<?= $autor['id_Autor'] ?>', '<?= htmlspecialchars($autor['Nombre']) ?>')">
                                        Editar
                                    </button>
            
                                    <!-- Botón Borrar -->
                                    <form action="verAutores.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $autor['id_Autor'] ?>">
                                        <button class="btn-borrar" type="submit" name="accion" value="borrar"
                                            onclick="return confirm('¿Estás seguro de que quieres borrar este autor?')">
                                            Borrar
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
    // Función para limpiar el formulario después de guardar o actualizar
    function limpiarFormulario() {
        const form = document.getElementById('autor-form');
        if (form) {
            form.reset(); // Restablece todos los campos del formulario
            document.getElementById('id').value = ''; // Limpia el campo ID manualmente
        }
    }

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
    </script>
</body>

</html>