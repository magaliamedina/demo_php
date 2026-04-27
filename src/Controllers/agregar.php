<?php
include_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // trim() elimina espacios vacíos al inicio y final
    $tarea = trim($_POST['titulo_tarea']);

    if (!empty($tarea)) {
        $sql = "INSERT INTO tareas (tarea) VALUES (?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$tarea]);
        header("Location: ../../public/index.php"); 
    } else {
        // Si está vacío, mandamos un error por la URL
        header("Location: ../../public/index.php?error=vacio");
    }
}