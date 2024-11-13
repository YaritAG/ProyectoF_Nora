<?php
require_once '../admin/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['RegistrarUser'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    if (empty($nombre) || empty($apellido) || empty($correo) || empty($password)) {
        die("Todos los campos son obligatorios.");
    }

    $conn = GetConexion();

    // Verificar si el correo ya está registrado
    $sql_check = "SELECT * FROM tpersonas WHERE Correo = :correo";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bindParam(':correo', $correo);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        die("El correo electrónico ya está registrado.");
    }

    // Determinar el rol basado en el correo
    $rol = ($correo === 'admin@miempresa.com') ? 'admin' : 'usuario';

    // Insertar el usuario en la base de datos sin encriptar la contraseña
    $sql = "INSERT INTO tpersonas (Nombre, Apellido, Correo, Password, rol) 
            VALUES (:nombre, :apellido, :correo, :password, :rol)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':password', $password); // Sin hashing
    $stmt->bindParam(':rol', $rol);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit;
    } else {
        echo "Error al registrar: " . $stmt->errorInfo()[2];
    }
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="es">
    <head>
        <title>My Biblio | Registro</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="static/register.css">

        <!-- Quicksand -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&display=swap"
            rel="stylesheet">
    </head>
    <body>
        <div class="container">
            <div class="signup-box">
                
                <h2>REGISTRARSE</h2>

                <?php session_start(); if (isset($_SESSION['message'])): ?>
                <p class="message">
                    <?= $_SESSION['message']; ?>
                </p>
                <?php unset($_SESSION['message']); ?>
                <?php endif; ?>
                
                <form action="register.php" method="POST">
                    <div class="input-box">
                        <label for="nombre">Nombre(s)</label><br>
                        <input type="text" id="nombre" name="nombre" placeholder="Ingresa tu Nombre" required>
                        <i class="icon user"></i>
                    </div>
                    <div class="input-box">
                        <label for="apellido">Apellido(s)</label><br>
                        <input type="text" id="apellido" name="apellido" placeholder="Ingresa tu Apellido" required>
                        <i class="icon user"></i>
                    </div>
                    <div class="input-box">
                        <label for="email">Email</label><br>
                        <input type="email" id="email" name="correo" placeholder="Ingresa tu Correo" required>
                        <i class="icon email"></i>
                    </div>
                    <div class="input-box">
                        <label for="contraseña">Contraseña</label><br>
                        <input type="password" id="password" name="password" placeholder="Ingresa tu Contraseña" required>
                        <i class="icon lock"></i>
                    </div>
                    <div class="recordarme">
                        <input type="checkbox" id="remember">
                        <label for="remember">Recordarme</label>
                    </div>

                    <!-- INPUT -->
                    <input type="submit" class="btn" name="RegistrarUser" value="Registrar">
                    
                    <div class="returns">
                        <a href="../../templates/biblio.html" class="return-menu">Regresar al menú</a>
                        <p>
                            ¿Ya tienes una cuenta? <a href="login.php" class="return-login">Inicia Sesión</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>