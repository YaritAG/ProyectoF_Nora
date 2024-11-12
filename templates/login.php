<?php
session_start();
require_once 'db.php';

// Si ya existe una sesión activa, redirige a la página del menú
if (isset($_SESSION['user_id'])) {
    header('Location: ../templates/menu.html');
    exit;
}

// Verifica si se han enviado datos del formulario
if (!empty($_POST['correo']) && !empty($_POST['password'])) {
    // Preparamos la consulta para obtener el correo y la contraseña de la base de datos
    $sql = "SELECT id, Correo, Password FROM tpersonas WHERE Correo = :correo";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':correo', $_POST['correo']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Variable para mensajes de error
    $message = '';

    // Verificamos si el correo existe y si la contraseña coincide usando password_verify
    if ($result && password_verify($_POST['password'], $result['Password'])) {
        $_SESSION['user_id'] = $result['id']; // Guardamos el ID de usuario en la sesión
        header("Location: ../templates/menu.html"); // Redirige al menú
        exit;
    } else {
        $message = 'Lo siento, las credenciales no coinciden'; // Mensaje de error si las credenciales no coinciden
    }
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/static/login.css">
    <title>Iniciar Sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Quicksand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;900&family=Quicksand:wght@300;700&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="login-box">
        <h1 class="login-txt">INICIAR SESIÓN</h1>
        
        <!-- Sección Izquierda-->
        <div class="seccion-izq">
            
            <h2 class="login-txt2">Bienevnido de vuelta</h2>
            <h3 class="login-txt3">Ingresa tu correo y tu contraseña para acceder a MyBiblio :D</h3>
           
            <?php session_start(); if (isset($_SESSION['message'])): ?>
            <p class="message">
                <?= $_SESSION['message']; ?>
            </p>
            <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            
            <!-- Sección del input del email -->
            <form action="../public/procesar_login.php" method="POST">
                <div class="input-box">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="correo" placeholder="Ingresa tu Correo" required>
                    <i class="icon email"></i>
                </div>
            
                <div class="input-box">
                    <label for="contraseña">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Ingresa tu Contraseña" required>
                    <i class="icon lock"></i>
                </div>
            
                <input type="submit" class="btn" name="LogUser" value="Iniciar Sesión">
            </form>

            <!-- URL para regresar directo al menú -->
            <div class="returns">
                <a href="biblio.html" class="return-menu">Regresar al menú</a>
                <p>
                    ¿No tienes una cuenta? <a href="register.html" class="return-login">Registrate</a> Aquí
                </p>
            </div>
        </div>

        <!-- Separador aquí -->
        <div class="separator"></div> 

        <div class="seccion-der">
            <!-- Imagen de la parte derecha -->
            <img class="img-login" src="imgs/login/MyBiblio.png" alt="">
        </div>
    </div>
</body>

</html>