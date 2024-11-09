<?php
    $servername = "localhost";
    $database = "biblioteca";
    $username = "root";
    $password = "";

    // Crear Conexión
    $conn = mysqli_connect($servername, $username, $password, $database);
    // Checar conexión
    if (!$conn) {
        die("Conexión Fallida: ". mysqli_connect_error());
    }

    echo "Conexión Exitosa";
    mysqli_close($conn);
?>