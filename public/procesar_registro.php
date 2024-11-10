<?php

require_once 'db.php';

// Verificar si los datos fueron enviados a través del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['RegistrarUser'])) {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Verificar si alguno de los campos está vacío
    if (empty($nombre) || empty($apellido) || empty($correo) || empty($password)) {
        die("Todos los campos son obligatorios.");
    }

    // Conectar a la base de datos
    $conn = GetConexion();

    // Comprobar si el correo ya está registrado
    $sql_check = "SELECT * FROM tpersonas WHERE Correo = :correo";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bindParam(':correo', $correo);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        die("El correo electrónico ya está registrado.");
    }

    // Preparar y ejecutar la consulta para insertar los datos en la base de datos
    $sql = "INSERT INTO tpersonas (Nombre, Apellido, Correo, Password) 
            VALUES (:nombre, :apellido, :correo, :password)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        echo "Registro exitoso";
        // Redireccionar a una página de confirmación o al login
        // header("Location: login.html");
        // exit;
    } else {
        echo "Error al registrar: " . $stmt->errorInfo()[2];
    }
}
?>