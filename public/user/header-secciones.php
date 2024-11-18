
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <!-- Nav Bar -->
    <nav class="barra-superior">
        <div class="botones-navbar">
            <a href="prestamos.php" class="btn-prestamo">Realizar Prestamo</a>
            <a href="generos.php" class="btn-generos">Géneros</a>
            <a href="../../templates/about.html" class="btn-about">Acerca de</a>
            <a href="../../templates/menu.php" class="btn-menu">Menú</a>
            <a href="libros.php" class="btn-libros">Libros</a>

            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="../admin/ventanaAdmin.php" class="btn-admin">Panel de Administración</a>
            <?php endif; ?>
        </div>

        <div class="logout">
            <a href="perfil.php" class="btn-perfil">Mi Perfil</a>
            <a href="login.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <style>
        /* ----------------------------------------
        /   Sección de la navbar
        / ---------------------------------------- */

        /* Estilos de la Barra Superior */
        .barra-superior {
            font-family: 'Quicksand';
            width: 96.2%;
            background-color: #030504;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            top: 0;
            height: 40px;
            position: sticky;
            padding: 10px 20px;
            z-index: 10;
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(3px);
            border-radius: 5px;
            box-shadow: 0 5px 30px 0 rgba(0, 50, 0, 0.7);
            padding-right: 35px;
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        /* Botones en la Barra Superior */
        .btn-prestamo,
        .btn-generos,
        .btn-about,
        .btn-libros,
        .btn-admin,
        .btn-logout,
        .btn-perfil,
        .btn-menu {
            margin-right: 20px;
            border-radius: 5px;
            padding: 6px 10px;
        }

        /* Enlaces dentro de los Botones en la Barra Superior */
        a.btn-prestamo,
        a.btn-generos,
        a.btn-about,
        a.btn-libros,
        a.btn-admin,
        a.btn-logout,
        a.btn-perfil,
        a.btn-menu {
            text-decoration: none;
            color: inherit;
            position: relative;
            display: inline-block;
            overflow: hidden;
        }

        /* Efecto Hover para los Enlaces en la Barra Superior */
        a.btn-prestamo::before,
        a.btn-generos::before,
        a.btn-about::before,
        a.btn-libros::before,
        a.btn-admin::before,
        a.btn-logout::before,
        a.btn-perfil::before,
        a.btn-menu::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 2px;
            background: #fff;
            border-radius: 5px;
            transform: scaleX(0);
            transition: all 0.5s ease;
            bottom: 0;
            left: 0;
        }

        a.btn-prestamo:hover::before,
        a.btn-generos:hover::before,
        a.btn-about:hover::before,
        a.btn-libros:hover::before,
        a.btn-admin:hover::before,
        a.btn-logout:hover::before,
        a.btn-perfil:hover::before,
        a.btn-menu:hover::before {
            transform: scaleX(1);
            /* Expande la línea de subrayado en hover */
        }

        .imagen-cabecera {
            width: 100%;
            height: auto;
            margin: 0;
            left: 0;
        }

        .img-cabecera {
            width: 101%;
            margin-left: -6px;
            margin-top: -6px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.25);
        }
    </style>
</body>

</html>