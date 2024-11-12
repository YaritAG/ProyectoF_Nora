<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['RegistrarUser'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $password = $_POST['password']; // Guarda sin hash

    if (empty($nombre) || empty($apellido) || empty($correo) || empty($password)) {
        die("Todos los campos son obligatorios.");
    }

    $conn = GetConexion();

    $sql_check = "SELECT * FROM tpersonas WHERE Correo = :correo";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bindParam(':correo', $correo);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        die("El correo electrónico ya está registrado.");
    }

    $sql = "INSERT INTO tpersonas (Nombre, Apellido, Correo, Password) 
            VALUES (:nombre, :apellido, :correo, :password)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':password', $password); // Sin hashing

    if ($stmt->execute()) {
        echo "Registro exitoso";
        header("Location: ../templates/login.html");
    } else {
        echo "Error al registrar: " . $stmt->errorInfo()[2];
    }
}
?>