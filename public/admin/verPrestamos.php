<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Verificación de sesión y permisos
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../../templates/menu.php');
    exit;
}

// Conexión a la base de datos
require 'db.php';
$conn = getConexion();

// Obtener préstamos agrupados por usuarios
try {
    $query = "
        SELECT 
            p.id_Personas AS user_id,
            p.Nombre AS user_nombre,
            p.Apellido AS user_apellido,
            p.Correo AS user_correo,
            tp.id_Prestamo,
            tp.fecha_prestamo,
            tp.fecha_devolucion,
            tp.Devuelto,
            GROUP_CONCAT(tl.Nombre SEPARATOR ', ') AS libros
        FROM 
            tpersonas p
        JOIN tprestamo tp ON p.id_Personas = tp.TPersonas_id_Personas
        JOIN tlibros tl ON tp.TLibros_id_Libro = tl.id_Libro
        GROUP BY p.id_Personas, tp.id_Prestamo
        ORDER BY p.id_Personas, tp.fecha_prestamo DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $prestamosPorUsuario = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los préstamos: " . $e->getMessage());
}

include '../../templates/a.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Préstamos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="static/tablasAdmin.css?=ver<?php echo time(); ?>">
</head>

<body>
    <div class="container">
        <h1>Registro de Préstamos</h1>

        <?php
        $currentUserId = null; // Variable para controlar cuándo cambiar de usuario
        foreach ($prestamosPorUsuario as $prestamo):
            if ($currentUserId !== $prestamo['user_id']):
                if ($currentUserId !== null): ?>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Nueva sección para cada usuario -->
            <div class="seccion-tabla">
                <h3>Préstamos de <?= htmlspecialchars($prestamo['user_nombre'] . " " . $prestamo['user_apellido']) ?></h3>
                <h3><b>Correo:</b> <?= htmlspecialchars($prestamo['user_correo']) ?></h3>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Préstamo</th>
                                <th>Libro(s) Prestado(s)</th>
                                <th>Fecha de Prestamo</th>
                                <th>Fecha de Devolución</th>
                                <th>Devuelto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $currentUserId = $prestamo['user_id'];
            endif;
            ?>

                        <!-- Filas de préstamos por usuario -->
                        <tr>
                            <td><?= htmlspecialchars($prestamo['id_Prestamo']) ?></td>
                            <td><?= htmlspecialchars($prestamo['libros']) ?></td>
                            <td><?= htmlspecialchars($prestamo['fecha_prestamo']) ?></td>
                            <td><?= $prestamo['fecha_devolucion'] ? htmlspecialchars($prestamo['fecha_devolucion']) : 'No asignada' ?>
                            </td>
                            <td><?= $prestamo['Devuelto'] ? 'Sí' : 'No' ?></td>
                            <td>
                                <!-- Botón para marcar como devuelto -->
                                <form method="POST" action="procesarDevolucion.php">
                                    <input type="hidden" name="prestamo_id"
                                        value="<?= htmlspecialchars($prestamo['id_Prestamo']) ?>">
                                    <button type="submit" onclick="return confirm('¿Confirmar devolución del préstamo?')">
                                        Marcar como Devuelto
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Cerrar última tabla -->
                    <?php if ($currentUserId !== null): ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
    </div>
</body>

</html>