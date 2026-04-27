<?php
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tarea = trim($_POST['titulo_tarea']);
    $cat_id = $_POST['categoria_id'];
    // Si no eligen fecha, la guardamos como NULL
    $fecha = !empty($_POST['fecha_vencimiento']) ? $_POST['fecha_vencimiento'] : null;

    if (!empty($tarea)) {
        $sql = "INSERT INTO tareas (tarea, categoria_id, fecha_vencimiento) VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$tarea, $cat_id, $fecha]);
        header("Location: ../../public/index.php?status=success");
    } else {
        header("Location: ../../public/index.php?error=vacio");
    }
}