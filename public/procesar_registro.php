<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "biblioteca";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si los datos fueron enviados a través del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];  // 'nombre' en minúsculas
    $apellido = $_POST['apellido'];  // 'apellido' en minúsculas
    $correo = $_POST['correo'];  // 'correo' en minúsculas
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // 'password' en minúsculas

    // Verificar si alguno de los campos está vacío
    if (empty($nombre) || empty($apellido) || empty($correo) || empty($password)) {
        die("Todos los campos son obligatorios.");
    }

    // Comprobar si el correo ya está registrado
    $sql_check = "SELECT * FROM tpersonas WHERE Correo = '$correo'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        die("El correo electrónico ya está registrado.");
    }

    // Preparar y ejecutar la consulta para insertar los datos en la base de datos
    $sql = "INSERT INTO tpersonas (Nombre, Apellido, Correo, Password) 
            VALUES ('$nombre', '$apellido', '$correo', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "Registro exitoso";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
}
?>