<?php
require_once 'db.php'; // Asegúrate de que la conexión a la base de datos esté correcta

$message = '';

if (!empty($_POST['nombre']) && !empty($_POST['apellido']) && !empty($_POST['correo']) && !empty($_POST['password'])) {
    // Preparar la consulta para insertar un nuevo usuario
    $sql = "INSERT INTO tpersonas (Nombre, Apellido, Correo, Password) VALUES (:nombre, :apellido, :correo, :password)";
    $stmt = $conn->prepare($sql);

    // Vincular los parámetros
    $stmt->bindParam(':nombre', $_POST['nombre']);
    $stmt->bindParam(':apellido', $_POST['apellido']);
    $stmt->bindParam(':correo', $_POST['correo']);

    // Encriptar la contraseña antes de insertarla en la base de datos
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $stmt->bindParam(':password', $password);

    // Ejecutar la consulta y verificar si se inserta correctamente
    if ($stmt->execute()) {
        $message = 'Usuario creado con éxito'; // Mensaje si la inserción fue exitosa
    } else {
        $message = 'Lo siento, hubo un problema al crear tu cuenta'; // Mensaje si hubo un error
    }
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="es">
    <head>
        <title>My Biblio | Registro</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../assets/static/register.css">

        <!-- Quicksand -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&display=swap"
            rel="stylesheet">
    </head>
    <body>
        <div class="container">
            <div class="signup-box">testo testo blah blah
                <h2>REGISTRARSE</h2>

                <?php session_start(); if (isset($_SESSION['message'])): ?>
                <p class="message">
                    <?= $_SESSION['message']; ?>
                </p>
                <?php unset($_SESSION['message']); ?>
                <?php endif; ?>
                
                <form action="../public/procesar_registro.php" method="POST">
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
                        <a href="biblio.html" class="return-menu">Regresar al menú</a>
                        <p>
                            ¿Ya tienes una cuenta? <a href="login.html" class="return-login">Inicia Sesión</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
        </body>
</html>