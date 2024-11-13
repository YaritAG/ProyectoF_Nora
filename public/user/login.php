<?php
session_start();
require_once '../admin/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['LogUser'])) {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    if (empty($correo) || empty($password)) {
        $_SESSION['message'] = "Todos los campos son obligatorios.";
        header("Location: ../../templates/login.html");
        exit;
    }

    $conn = GetConexion();

    $sql = "SELECT Password FROM tpersonas WHERE Correo = :correo";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':correo', $correo);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $storedPassword = $result['Password'];

        if ($password === $storedPassword) { // Comparación directa
            header("Location: ../../templates/menu.html");
            exit;
        } else {
            $_SESSION['message'] = "Contraseña incorrecta.";
            header("Location: ../../templates/login.html");
            exit;
        }
    } else {
        $_SESSION['message'] = "La cuenta no está registrada.";
        header("Location: ../../templates/login.html");
        exit;
    }
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../../assets/static/login.css">
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

            <!-- Mostrar mensaje si las credenciales no coinciden -->
            <?php if (!empty($message)): ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>

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
            <img class="img-login" src="imgs/login/MyBiblio.png" alt="">
        </div>
    </div>
</body>

</html>