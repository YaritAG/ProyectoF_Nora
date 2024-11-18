<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Verifica si el usuario está logueado y tiene rol de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../../templates/menu.php');
    exit;
}

require 'db.php'; // Archivo de conexión a la base de datos
$conn = getConexion();

// Inicialización de variables
$id = $genero = $descripcion = $imagenRuta = "";
$accion = $_POST['accion'] ?? "";
$queryString = $_GET['query'] ?? "";

// Manejar acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $genero = $_POST['genero'] ?? "";
    $descripcion = $_POST['descripcion'] ?? "";
    $imagenRuta = null;

    // Manejar la imagen subida
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $directorioSubida = '../../assets/imgs/generos/';
        if (!is_dir($directorioSubida)) {
            mkdir($directorioSubida, 0755, true);
        }
        $nombreArchivo = uniqid() . '_' . basename($_FILES['imagen']['name']);
        $rutaCompleta = $directorioSubida . $nombreArchivo;

        // Validar el tamaño y formato de la imagen
        if ($_FILES['imagen']['size'] > 2 * 1024 * 1024) {
            $_SESSION['mensaje'] = "El archivo es demasiado grande. Máximo permitido: 2 MB.";
            header('Location: verGeneros.php');
            exit;
        }

        $formatosPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['imagen']['type'], $formatosPermitidos)) {
            $_SESSION['mensaje'] = "Formato de archivo no permitido. Solo se permiten imágenes JPEG, PNG y GIF.";
            header('Location: verGeneros.php');
            exit;
        }

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaCompleta)) {
            $imagenRuta = 'assets/imgs/generos/' . $nombreArchivo;
        } else {
            $_SESSION['mensaje'] = "Error al subir la imagen.";
            header('Location: verGeneros.php');
            exit;
        }
    }

    // Crear o actualizar género
    if ($accion === 'actualizar') {
        if (empty($id)) {
            $stmt = $conn->prepare("INSERT INTO tgenero (Nombre, Descripcion, Imagen) VALUES (:nombre, :descripcion, :imagen)");
            $stmt->bindParam(':nombre', $genero);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':imagen', $imagenRuta);
            $stmt->execute();
            $_SESSION['mensaje'] = "Género agregado correctamente.";
        } else {
            if ($imagenRuta) {
                $stmt = $conn->prepare("UPDATE tgenero SET Nombre = :nombre, Descripcion = :descripcion, Imagen = :imagen WHERE id_Genero = :id");
                $stmt->bindParam(':imagen', $imagenRuta);
            } else {
                $stmt = $conn->prepare("UPDATE tgenero SET Nombre = :nombre, Descripcion = :descripcion WHERE id_Genero = :id");
            }
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nombre', $genero);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->execute();
            $_SESSION['mensaje'] = "Género actualizado correctamente.";
        }
    }

    // Eliminar género
    if ($accion === 'borrar') {
        $stmt = $conn->prepare("DELETE FROM tgenero WHERE id_Genero = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $_SESSION['mensaje'] = "Género eliminado correctamente.";
    }

    // Enviar género a la página principal
    if ($accion === 'enviar') {
        $stmt = $conn->prepare("UPDATE tgenero SET Agregado = 1, FechaAgregado = NOW() WHERE id_Genero = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $_SESSION['mensaje'] = "Género enviado correctamente a la página principal.";
    }

    header('Location: verGeneros.php');
    exit;
}

// Leer géneros de la base de datos con filtro
$sql = "SELECT id_Genero, Nombre, Descripcion, Imagen, Agregado FROM tgenero";
$params = [];
if (!empty($queryString)) {
    $sql .= " WHERE id_Genero LIKE ? OR Nombre LIKE ?";
    $params = ["%$queryString%", "%$queryString%"];
}
$sql .= " ORDER BY Nombre ASC";
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
    <link rel="stylesheet" href="static/tablasAdmin.css?ver=<?php echo time(); ?>">
</head>

<body>
    <div class="container">
        <h1>Tabla de Géneros</h1>
        <div class="seccion-tabla">
             <!-- Mensaje de confirmación -->
        <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="mensaje"><?= $_SESSION['mensaje'] ?></div>
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>
            
            <!-- Buscador -->
            <div class="seccion-buscador">
                <form method="GET" action="verGeneros.php">
                    <input type="text" class="buscador" placeholder="Buscar por ID o Nombre..." name="query"
                        value="<?= htmlspecialchars($queryString ?? '') ?>" aria-label="Buscar">
                    <button class="lupa" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            
            <div class="inputs">
                <div class="editors">
                    <h3>Agregar o Editar Género</h3>
                    <form action="verGeneros.php" method="POST" enctype="multipart/form-data">
                        <div class="seccion-2">
                            <input type="hidden" id="id" name="id" value="<?= htmlspecialchars($id ?? '') ?>">
                            
                            <label for="genero">Nombre del Género:</label> <br><br>
                            <input class="input-editor" type="text" id="genero" name="genero" value="<?= htmlspecialchars($genero ?? '') ?>" required><br><br>
                            
                            <label for="descripcion">Descripción:</label><br>
                            <textarea class="input-editor" id="descripcion" name="descripcion" rows="5" required><?= htmlspecialchars($descripcion ?? '') ?></textarea><br><br>
                            
                            <label for="imagen">Seleccionar Imagen:</label><br>
                            <input  type="file" id="imagen" name="imagen" accept="image/*"><br>
                        </div> 

                        <div class="buttons"> 
                            <button type="submit" name="accion" value="actualizar" class="btn-save">Guardar</button>
                            <button type="reset" class="btn-cancel">Cancelar</button>
                        </div>   
                    </form>
                </div>
            </div>
            
            <!-- Tabla de Géneros -->
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Género</th>
                            <th>Descripción</th>
                            <th>Imagen</th>
                            <th>Agregar Sección</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($generos as $genero): ?>
                            <tr class="<?= $genero['Agregado'] ? 'enviado' : '' ?>">
                                <td><?= htmlspecialchars($genero['id_Genero']) ?></td>
                                <td><?= htmlspecialchars($genero['Nombre']) ?></td>
                                <td><?= htmlspecialchars($genero['Descripcion']) ?></td>
                                <td>
                                    <?php if (!empty($genero['Imagen'])): ?>
                                        <img src="<?= htmlspecialchars($genero['Imagen']) ?>" alt="Imagen del género"
                                            style="width: 50px; height: 50px;">
                                    <?php else: ?>
                                        <span>No Imagen</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($genero['Agregado']): ?>
                                        <button disabled>Enviado</button>
                                    <?php else: ?>
                                        <form action="verGeneros.php" method="POST" onsubmit="return confirmarEnvio();">
                                            <input type="hidden" name="id" value="<?= $genero['id_Genero'] ?>">
                                            <button type="submit" name="accion" value="enviar" class="send-form">Enviar</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form action="verGeneros.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $genero['id_Genero'] ?>">
                                        <button type="submit" name="accion" value="editar" class="btn-editar">Editar</button>
                                    </form><br>
                                    <form action="verGeneros.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $genero['id_Genero'] ?>">
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

    <script>
        function confirmarEnvio() {
            return confirm("¿Estás seguro de que quieres enviar este género a la página principal?");
        }
    </script>
</body>

</html>