<?php
// db.php
function GetConexion()
{
    $servername = "localhost";
    $database = "biblioteca";
    $username = "root";
    $password = "";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Conexión Fallida: " . $e->getMessage());
    }
}
?>