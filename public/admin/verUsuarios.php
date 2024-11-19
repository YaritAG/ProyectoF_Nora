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

require 'db.php'; // Incluye el archivo de conexión a la base de datos
$conn = getConexion();

// -----------------------------------------------------------------------
// Buscar Datos
// -----------------------------------------------------------------------
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Construye la consulta SQL base
$sql = "SELECT id_Personas AS id, Nombre AS nombre, Apellido AS apellido, Correo AS correo, Password AS password, Rol AS rol FROM tpersonas";

// Si hay un término de búsqueda, añade una cláusula WHERE
if (!empty($query)) {
    $sql .= " WHERE 
              id_Personas LIKE :query OR
              Nombre LIKE :query OR
              Apellido LIKE :query OR
              Correo LIKE :query OR
              Rol LIKE :query";
}

$stmt = $conn->prepare($sql);

// Agrega el término de búsqueda con comodines si está definido
if (!empty($query)) {
    $stmt->bindValue(':query', '%' . $query . '%');
}

// Ejecuta la consulta y maneja errores de depuración
try {
    $stmt->execute();
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
    exit();
}

// Muestra los resultados obtenidos en la búsqueda en el HTML

// -----------------------------------------------------------------------
// Eliminar Datos
// -----------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id = $_POST['id'];
    $sql = "DELETE FROM tpersonas WHERE id_Personas = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo "Registro eliminado con éxito";
    } else {
        echo "Error al eliminar el registro";
    }
    // Redirige de vuelta a la misma página para ver los cambios
    header('Location: verUsuarios.php');
    exit;
}

// ------------------------------------------------------------------------
// Actualizar Datos
// ------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    $sql = "UPDATE tpersonas SET Nombre = :nombre, Apellido = :apellido, Correo = :correo, Password = :password, Rol = :rol WHERE id_Personas = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':rol', $rol);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo "Registro actualizado con éxito";
    } else {
        echo "Error al actualizar el registro";
    }

    // Redirige de vuelta a la misma página para ver los cambios
    header('Location: verUsuarios.php');
    exit;
}
?>

<?php include '../../templates/a.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Usuarios</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="static/tablasAdmin.css?ver=<?php echo time(); ?>">">
</head>

<body>
    <div class="container">
        <h1>Tabla de Usuarios</h1>

        <div class="seccion-tabla">
                          
            <!-- Buscador con método GET para obtener datos -->    
            <div class="seccion-buscador">
                <form method="GET" action="verUsuarios.php">
                    <input type="text" class="buscador" placeholder="Buscar..." name="query" aria-label="Buscar">
                    <button class="lupa" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            
            <div class="inputs">
                <div class="editor">
                    <h3>Editar o Agregar Usuario</h3>

                    <form id="editor-form" method="POST" action="verUsuarios.php">
                        <!-- Campo oculto para almacenar el ID del usuario que se está editando -->
                        <input type="hidden" id="id" name="id">

                        <div class="seccion-1">                        
                            <label for="nombre">Nombre:</label>
                            <input class="input-editor" type="text" id="nombre" name="nombre" required><br><br>
                        </div>
                        
                        <div class="seccion-2">
                            <label for="apellido">Apellido:</label>
                            <input class="input-editor" type="text" id="apellido" name="apellido" required><br><br>
                            
                            <label for="correo">Correo:</label>
                            <input class="input-editor" type="email" id="correo" name="correo" required><br><br>
                        </div>

                        <div class="seccion-3">
                            <label for="password">Password:</label>
                            <input class="input-editor" type="password" id="password" name="password" required><br><br>
                            
                            <label for="rol">Rol:</label>
                            <select class="select-editor" id="rol" name="rol" required>
                                <option value="admin">Admin</option>
                                <option value="usuario">Usuario</option>
                            </select><br><br>
                        </div>
                        
                        <!-- Contenedor para los botones -->
                        <div class="buttons">
                            <button class="btn-save" type="submit" name="accion" value="actualizar">Guardar</button>
                            <button class="btn-cancel" type="reset">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>  

            <!-- Tabla -->
            <div class="table-outer-wrapper">
                <div class="scrollbar-top">
                    <div class="scroll-sync"></div> <!-- Contenido para sincronización de scroll -->
                </div>

                <!-- Tabla de Usuarios -->
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Correo</th>
                                <th>Password</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Recorre los resultados y crea una fila para cada usuario
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['apellido']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['correo']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['password']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['rol']) . "</td>";
                                echo "<td>
                                        <form method='POST' action='verUsuarios.php' style='display:inline;' onsubmit='return confirmarBorrado();'>
                                            <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                                            <button type='submit' name='accion' value='eliminar' class='btn-borrar'>Borrar</button>
                                        </form>
                                        <button type='button' class='btn-editar' onclick='editarUsuario(" . $row['id'] . ", `" . htmlspecialchars($row['nombre']) . "`, `" . htmlspecialchars($row['apellido']) . "`, `" . htmlspecialchars($row['correo']) . "`, `" . htmlspecialchars($row['password']) . "`, `" . htmlspecialchars($row['rol']) . "`)' >Editar</button>
                                    </td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <script>
                    // Función para confirmar antes de eliminar
                    function confirmarBorrado() {
                        return confirm("¿Estás seguro de que deseas eliminar este registro?");
                    }

                    // Función para cargar los datos en el formulario de edición
                    function editarUsuario(id, nombre, apellido, correo, password, rol) {
                        document.getElementById('id').value = id;
                        document.getElementById('nombre').value = nombre;
                        document.getElementById('apellido').value = apellido;
                        document.getElementById('correo').value = correo;
                        document.getElementById('password').value = password;
                        document.getElementById('rol').value = rol;
                    }
                </script>

            </div>
        </div>
    </div>

    <script>
        function editarUsuario(id, nombre, apellido, correo, password, rol) {
            // Cargar los datos en los inputs del formulario
            document.getElementById('id').value = id;
            document.getElementById('nombre').value = nombre;
            document.getElementById('apellido').value = apellido;
            document.getElementById('correo').value = correo;
            document.getElementById('password').value = password;
            document.getElementById('rol').value = rol;
        }
    </script>
</body>

</html>