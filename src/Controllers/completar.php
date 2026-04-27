<?php
include_once __DIR__ . '/../../config/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Primero consultamos el estado actual
    $consulta = $conexion->prepare("SELECT completada FROM tareas WHERE id = ?");
    $consulta->execute([$id]);
    $tarea = $consulta->fetch();

    // Si estaba en 0, la ponemos en 1. Si estaba en 1, la ponemos en 0.
    $nuevo_estado = ($tarea['completada'] == 0) ? 1 : 0;

    // Actualizamos
    $sql = "UPDATE tareas SET completada = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$nuevo_estado, $id]);

    header("Location: ../../public/index.php");
}
?>