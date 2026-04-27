<?php
include 'db.php'; 

if (isset($conexion)) {
    echo "¡Conexión exitosa!";
} else {
    echo "La variable conexion no existe.";
}
?>