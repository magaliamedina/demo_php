<?php
// Configuración local

$host = "localhost";
$db   = "db_demo";
$user = "root";
$pass = "";
$charset = 'utf8mb4';

// Configuración para hosting remoto (descomentar y editar estos datos cuando subas a tu hosting)
/*$host = "sql111.infinityfree.com";
$db   = "if0_41770783_db_demo";
$user = "if0_41770783";
$pass = "uRlN4FhkFYdM";
$charset = 'utf8mb4';*/


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