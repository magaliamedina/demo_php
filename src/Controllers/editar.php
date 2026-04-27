<?php
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $tarea = trim($_POST['tarea']);
    $fecha = !empty($_POST['fecha_vencimiento']) ? $_POST['fecha_vencimiento'] : null;

    if (!empty($id) && !empty($tarea)) {
        $sql = "UPDATE tareas SET tarea = ?, fecha_vencimiento = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$tarea, $fecha, $id]);
    }
    header("Location: ../../public/index.php");
}