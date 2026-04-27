<?php
$host = "localhost";
$db   = "db_demo";
$user = "root";
$pass = "";
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    // Aquí creamos la variable $conexion
    $conexion = new PDO($dsn, $user, $pass);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
     echo "Error en la conexión: " . $e->getMessage();
     exit;
}
?>