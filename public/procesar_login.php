<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['LogUser'])) {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    if (empty($correo) || empty($password)) {
        $_SESSION['message'] = "Todos los campos son obligatorios.";
        header("Location: ../templates/login.html");
        exit;
    }

    $conn = GetConexion();

    // Consultamos la base de datos por el correo
    $sql = "SELECT Password FROM tpersonas WHERE Correo = :correo";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':correo', $correo);
    $stmt->execute();

    // Verificamos si el correo está registrado
    if ($stmt->rowCount() > 0) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $storedPassword = $result['Password'];

        if (password_verify($password, $storedPassword)) {
            echo "Contraseña correcta, redirigiendo...";
            header("Location: ../templates/menu.html");
            exit;
        }

        } else {
            // Contraseña incorrecta
            $_SESSION['message'] = "Contraseña incorrecta.";
            header("Location: ../templates/login.html");
            exit;
        }
    } else {
        // Cuenta no registrada
        $_SESSION['message'] = "La cuenta no está registrada.";
        header("Location: ../templates/login.html");
        exit;
    }
?>