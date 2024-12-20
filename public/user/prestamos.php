<?php
require '../admin/db.php';
session_start();

$conn = getConexion();

// Verificar si se realizó una solicitud de préstamo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'] ?? null; // ID del usuario logueado
    $libroId = $_POST['libro_id'] ?? null; // ID del libro enviado desde el formulario
    $fechaPrestamo = date('Y-m-d H:i:s'); // Fecha actual

    if ($userId && $libroId) {
        try {
            // Registrar el préstamo en la base de datos
            $stmt = $conn->prepare("
                INSERT INTO tprestamo (fecha_prestamo, TPersonas_id_Personas, TLibros_id_Libro, Devuelto) 
                VALUES (:fecha_prestamo, :user_id, :libro_id, 0)
            ");
            $stmt->bindParam(':fecha_prestamo', $fechaPrestamo);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':libro_id', $libroId);
            $stmt->execute();

            // Actualizar el número de ejemplares del libro
            $stmt = $conn->prepare("
                UPDATE tlibros 
                SET Ejemplar = Ejemplar - 1 
                WHERE id_Libro = :libro_id AND Ejemplar > 0
            ");
            $stmt->bindParam(':libro_id', $libroId);
            $stmt->execute();

            $_SESSION['mensaje'] = "¡Préstamo registrado exitosamente!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error al registrar el préstamo: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Hubo un error al registrar el préstamo. Verifique los datos enviados.";
    }

    // Redirigir para evitar reenvío del formulario
    header('Location: prestamos.php');
    exit;
}

// Verificar si se realiza una devolución
if (isset($_GET['devolver']) && isset($_GET['id_prestamo'])) {
    $idPrestamo = $_GET['id_prestamo'];
    $fechaDevolucion = date('Y-m-d H:i:s'); // Fecha de devolución actual

    try {
        // Actualizar el préstamo con la fecha de devolución y marcar como devuelto
        $stmt = $conn->prepare("
            UPDATE tprestamo 
            SET fecha_devolucion = :fecha_devolucion, Devuelto = 1 
            WHERE id_Prestamo = :id_prestamo
        ");
        $stmt->bindParam(':fecha_devolucion', $fechaDevolucion);
        $stmt->bindParam(':id_prestamo', $idPrestamo);
        $stmt->execute();

        // Restaurar un ejemplar al libro correspondiente
        $stmt = $conn->prepare("
            UPDATE tlibros 
            SET Ejemplar = Ejemplar + 1 
            WHERE id_Libro = (SELECT TLibros_id_Libro FROM tprestamo WHERE id_Prestamo = :id_prestamo)
        ");
        $stmt->bindParam(':id_prestamo', $idPrestamo);
        $stmt->execute();

        $_SESSION['mensaje'] = "¡Devolución registrada exitosamente!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al registrar la devolución: " . $e->getMessage();
    }

    // Redirigir para evitar reenvío del formulario
    header('Location: prestamos.php');
    exit;
}

// Consultar los préstamos del usuario actual
$stmt = $conn->prepare("
    SELECT tp.id_Prestamo, tl.Nombre AS libro, tp.fecha_prestamo, tp.fecha_devolucion, tp.Devuelto
    FROM tprestamo tp
    JOIN tlibros tl ON tp.TLibros_id_Libro = tl.id_Libro
    WHERE tp.TPersonas_id_Personas = :id_persona
");
$stmt->bindParam(':id_persona', $_SESSION['user_id']);
$stmt->execute();
$prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header-secciones.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos | MyBiblio</title>
    <link rel="stylesheet" href="static/prestamos.css?=ver<?php echo time(); ?>">

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Quicksand:wght@300..700&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="container">

        <!-- Mostrar mensajes -->
        <?php if (isset($_SESSION['mensaje'])): ?>
            <p class="mensaje"><?= $_SESSION['mensaje'] ?></p>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?= $_SESSION['error'] ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="seccion-tabla">
            <h1>Préstamos</h1>
            <h3>Aquí puedes ver tus libros prestados</h3>

            <table>
                <thead>
                    <tr>
                        <th>ID Préstamo</th>
                        <th>Libro</th>
                        <th>Fecha de Préstamo</th>
                        <th>Fecha de Devolución</th>
                        <th>Devuelto</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prestamos as $prestamo): ?>
                        <tr>
                            <td><?= htmlspecialchars($prestamo['id_Prestamo']) ?></td>
                            <td><?= htmlspecialchars($prestamo['libro']) ?></td>
                            <td><?= htmlspecialchars($prestamo['fecha_prestamo']) ?></td>
                            <td><?= $prestamo['fecha_devolucion'] && $prestamo['fecha_devolucion'] !== '0000-00-00'
                                ? htmlspecialchars($prestamo['fecha_devolucion'])
                                : 'No asignada' ?>
                            </td>
                            <td><?= $prestamo['Devuelto'] ? 'Sí' : 'No' ?></td>
                            <td>
                                <?php if (!$prestamo['Devuelto']): ?>
                                    <a href="prestamos.php?devolver=1&id_prestamo=<?= $prestamo['id_Prestamo'] ?>">Devolver</a>
                                <?php else: ?>
                                    Devuelto
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>