<?php
session_start();
require_once '../admin/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    // Conexión a la base de datos
    $conn = GetConexion();

    // Consulta para obtener el usuario con el correo ingresado y contraseña
    $query = "SELECT * FROM tpersonas WHERE Correo = :correo AND Password = :password";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario existe y si tiene rol de admin
    if ($usuario) {
        $_SESSION['user_id'] = $usuario['id_Personas'];
        $_SESSION['user_name'] = $usuario['Nombre'];
        $_SESSION['user_role'] = $usuario['rol']; // Guarda el rol en la sesión

        // Redirigir basado en el rol
        if ($usuario['rol'] === 'admin') {
            header('Location: ../admin/ventanaAdmin.php'); // Redirige a una página de administrador
        } else {
            header('Location: ../../templates/menu.php'); // Redirige a una página de usuario regular
        }
        exit;
    } else {
        echo "Credenciales incorrectas.";
    }
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="static/login.css">
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

            <h2 class="login-txt2">Bienvenido de vuelta</h2>
            <h3 class="login-txt3">Ingresa tu correo y tu contraseña para acceder a MyBiblio :D</h3>


            <!-- Sección del input del email -->
            <form action="login.php" method="POST">
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

            <!-- Mostrar mensaje si las credenciales no coinciden -->
            <?php if (!empty($message)): ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>

            <!-- URL para regresar directo al menú -->
            <div class="returns">
                <a href="../../templates/biblio.html" class="return-menu">Regresar al menú</a>
                <p>
                    ¿No tienes una cuenta? <a href="register.php" class="return-login">Regístrate</a> Aquí
                </p>
            </div>
        </div>

        <!-- Separador aquí -->
        <div class="separator"></div>

        <div class="seccion-der">
            <!-- Imagen de la parte derecha -->
            <img class="img-login" src="../../assets/imgs/login/MyBiblio.png" alt="">
        </div>
    </div>
</body>

</html>