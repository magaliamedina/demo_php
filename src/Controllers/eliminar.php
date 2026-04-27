<?php
include_once __DIR__ . '/../../config/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM tareas WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id]);

    header("Location: ../../public/index.php");
}
?>