<?php
require '../admin/db.php';
session_start();

$conn = getConexion();

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
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos | MyBiblio</title>
    <link rel="stylesheet" href="static/prestamos.css">
</head>

<body>
    <div class="container">
        <header>
            <?php include 'header-secciones.php'; ?>
        </header>

        <div class="seccion-principal">
            <h1>Préstamos</h1>
            <h3>Aquí puedes ver tus libros prestados</h3>

            <!-- Mostrar mensajes -->
            <?php if (isset($_SESSION['mensaje'])): ?>
                <p class="mensaje"><?= $_SESSION['mensaje'] ?></p>
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <p class="error"><?= $_SESSION['error'] ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Tabla de préstamos -->
            <table>
                <thead>
                    <tr>
                        <th>ID Préstamo</th>
                        <th>Libro</th>
                        <th>Fecha de Préstamo</th>
                        <th>Fecha de Devolución</th>
                        <th>Devuelto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prestamos as $prestamo): ?>
                        <tr>
                            <td><?= htmlspecialchars($prestamo['id_Prestamo']) ?></td>
                            <td><?= htmlspecialchars($prestamo['libro']) ?></td>
                            <td><?= htmlspecialchars($prestamo['fecha_prestamo']) ?></td>
                            <td><?= htmlspecialchars($prestamo['fecha_devolucion']) ?></td>
                            <td><?= $prestamo['Devuelto'] ? 'Sí' : 'No' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>