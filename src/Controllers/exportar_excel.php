<?php
include '../../config/db.php';

// Configuramos las cabeceras para que el navegador lo interprete como Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Reporte_Tareas_2026.xls");

// Consulta de datos
$sql = "SELECT t.tarea, c.nombre AS categoria, t.fecha_vencimiento, 
               IF(t.completada=1, 'Completada', 'Pendiente') as estado 
        FROM tareas t 
        LEFT JOIN categorias c ON t.categoria_id = c.id 
        ORDER BY t.id DESC";

$sentencia = $conexion->query($sql);

echo "<table border='1'>";
echo "<tr><th style='background-color: #D3D3D3;'>Tarea</th><th>Categoria</th><th>Vencimiento</th><th>Estado</th></tr>";

while ($row = $sentencia->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . utf8_decode($row['tarea']) . "</td>";
    echo "<td>" . utf8_decode($row['categoria']) . "</td>";
    echo "<td>" . ($row['fecha_vencimiento'] ?: 'S/N') . "</td>";
    echo "<td>" . $row['estado'] . "</td>";
    echo "</tr>";
}
echo "</table>";
?>