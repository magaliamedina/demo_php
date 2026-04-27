<?php 
// Punto de entrada principal. Usar la conexión ubicada en '../config/db.php'
include_once __DIR__ . '/../config/db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Demo de Tareas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome CDN for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="text-center mb-4">
                        <i class="fa-solid fa-clipboard-check me-2"></i>Mis Tareas
                    </h2>
                    
                    <form action="../src/Controllers/agregar.php" method="POST" class="d-flex mb-3">
                        <input type="text" name="titulo_tarea" class="form-control me-2" placeholder="Escribe una tarea..." required>
                        <button type="submit" class="btn btn-primary">Agregar</button>
                    </form>

                    <ul class="list-group">
                        <?php
                        $sentencia = $conexion->query("SELECT * FROM tareas ORDER BY id DESC");
                        $hayTareas = false;
                        while ($fila = $sentencia->fetch()) {
                            $hayTareas = true;
                            $completada = $fila['completada'];
                            // Si está completada, tachamos el texto y cambiamos color del botón
                            $spanClass = $completada ? "text-decoration-line-through text-muted" : "";
                            $btnCompletarClass = $completada ? "btn btn-success btn-sm me-2" : "btn btn-secondary btn-sm me-2";
                            $iconCompletar = $completada ? "fa-solid fa-check-circle" : "fa-regular fa-circle";
                            $tooltipCompletar = $completada ? "Marcar como pendiente" : "Marcar como completada";

                            echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                            echo "<div class='d-flex align-items-center'>";
                            // Botón para cambiar estado completada
                            echo "<a href='../src/Controllers/completar.php?id=" . $fila['id'] . "' class='$btnCompletarClass' title='$tooltipCompletar'>";
                            echo "<i class='$iconCompletar'></i>";
                            echo "</a>";
                            echo "<span class='$spanClass'>" . htmlspecialchars($fila['tarea']) . "</span>";
                            echo "</div>";
                            echo "<a href='../src/Controllers/eliminar.php?id=" . $fila['id'] . "' class='btn btn-danger btn-sm' title='Eliminar'>";
                            echo "<i class='fa-solid fa-trash'></i>";
                            echo "</a>";
                            echo "</li>";
                        }
                        if (!$hayTareas) {
                            echo "<li class='list-group-item text-center text-success fw-bold'>";
                            echo "<i class='fa-solid fa-face-smile-wink me-2'></i>No hay tareas pendientes. ¡Buen trabajo!";
                            echo "</li>";
                        }
                        ?>
                    </ul>
         
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="text-center mt-5 pb-4">
    <hr class="w-25 mx-auto mb-4 text-muted">
    <p class="text-secondary small">
        Developed with <i class="fa-solid fa-heart text-danger"></i> by 
        <span class="fw-bold text-dark">Magali Medina</span> &copy; 2026
    </p>
    <div class="d-flex justify-content-center gap-3">
        <a href="https://github.com/magaliamedina" class="text-secondary"><i class="fa-brands fa-github fa-lg"></i></a>
        <a href="https://www.linkedin.com/in/magali-anabel-medina/" class="text-secondary"><i class="fa-brands fa-linkedin fa-lg"></i></a>
    </div>
</footer>

</body>
</html>