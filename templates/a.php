<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventana Admin</title>

    <!-- Quicksand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&display=swap"
        rel="stylesheet">
</head>

<body>
    <nav class="barra-superior">
        <div class="botones-navbar">
            <a href="verLibros.php" class="btn-verLibros">Ver Libros</a>
            <a href="verPrestamos.php" class="btn-verPrestamos">Ver Prestamos</a>
            <a href="../../templates/menu.php" class="btn-menu">Menú Principal</a>
            <a href="ventanaAdmin.php" class="btn-admin">Menu de Admin</a>
            <a href="verUsuarios.php" class="btn-users">Ver Usuarios</a>
        </div>

        <div class="logout">
            <a href="../user/perfil.php" class="btn-perfil">Mi Perfil</a>
            <a href="../user/login.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <style>
        .barra-superior {
    font-family: 'Quicksand';
    width: 97.6%;
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
        box-shadow: 0 5px 30px 0 rgba(22, 32, 220, 0.37);
        padding-right: 35px;
        border: 1px solid rgba(255, 255, 255, 0.18);
}


.btn-verLibros,
.btn-verPrestamos,
.btn-admin,
.btn-libros,
.btn-users,
.btn-menu,
.btn-perfil,
.btn-logout {
    margin-right: 20px;
    border-radius: 5px;
    padding: 6px 10px;
}

a.btn-verLibros,
a.btn-verPrestamos,
a.btn-admin,
a.btn-libros,
a.btn-users,
a.btn-menu,
a.btn-perfil,
a.btn-logout {
    text-decoration: none;
    color: inherit; 
    position: relative;
    display: inline-block;
    overflow: hidden;
}


a.btn-verLibros::before,
a.btn-verPrestamos::before,
a.btn-admin::before,
a.btn-libros::before,
a.btn-users::before,
a.btn-menu::before,
a.btn-perfil::before,
a.btn-logout::before {
content: "";
    position: absolute;
    width: 100%;
    height: 2px;
    background: #fff;
    border-radius: 5px;
    transform: scaleX(0);
    transition: all .5s ease;
    bottom: 0;
    left: 0;
}

a.btn-verLibros:hover::before,
a.btn-verPrestamos:hover::before,
a.btn-admin:hover::before,
a.btn-libros:hover::before,
a.btn-users:hover::before,
a.btn-menu:hover::before,
a.btn-perfil:hover::before,
a.btn-logout:hover::before{
transform: scaleX(1);
}      
    </style> 
</body>

</html>