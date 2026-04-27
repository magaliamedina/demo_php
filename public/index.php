<?php 
include_once __DIR__ . '/../config/db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi Demo de Tareas | Magali Medina</title>
    <script>
        (function () {
            var saved = localStorage.getItem('demo_theme');
            document.documentElement.setAttribute('data-bs-theme', saved === 'light' ? 'light' : 'dark');
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Ajustes para que la web se vea mejor */
        .card { border-radius: 15px; border: none; }
        .list-group-item { border-left: none; border-right: none; transition: 0.3s; }
        [data-bs-theme="light"] .list-group-item:hover { background-color: #f8f9fa; }
        [data-bs-theme="dark"] .list-group-item:hover { background-color: rgba(255, 255, 255, 0.06); }
        
        /* Estilo para impresión / PDF */
        @media print {
            .btn, form, .btn-group, footer, .progress, .download-buttons {
                display: none !important;
            }
            .card { box-shadow: none !important; border: 1px solid #ddd !important; }
            body { background-color: white !important; }
            .container { width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="min-vh-100 bg-body">

<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-9 col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="position-relative mb-4">
                        <button type="button" id="themeToggle" class="btn btn-outline-secondary btn-sm position-absolute top-0 end-0 rounded-pill px-3" title="Cambiar tema claro / oscuro" aria-label="Cambiar tema">
                            <i class="fa-solid fa-moon" id="themeIconMoon"></i>
                            <i class="fa-solid fa-sun d-none" id="themeIconSun"></i>
                        </button>
                        <h2 class="text-center fs-3 fw-bold text-primary mb-0 pe-5">
                            <i class="fa-solid fa-clipboard-check me-2"></i>Gestor de Tareas
                        </h2>
                    </div>

                    <?php
                    $total_tareas = $conexion->query("SELECT COUNT(*) FROM tareas")->fetchColumn();
                    $completadas = $conexion->query("SELECT COUNT(*) FROM tareas WHERE completada = 1")->fetchColumn();
                    $vencidas = $conexion->query("SELECT COUNT(*) FROM tareas WHERE completada = 0 AND fecha_vencimiento < CURDATE()")->fetchColumn();
                    $porcentaje = ($total_tareas > 0) ? round(($completadas / $total_tareas) * 100) : 0;
                    ?>

                    <div class="row mb-3 text-center g-2">
                        <div class="col-4">
                            <div class="p-2 border rounded bg-body-secondary">
                                <small class="text-muted d-block">Total</small>
                                <span class="h5 fw-bold"><?php echo $total_tareas; ?></span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 border rounded bg-body-secondary">
                                <small class="text-muted d-block">Vencidas</small>
                                <span class="h5 fw-bold text-danger"><?php echo $vencidas; ?></span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 border rounded bg-body-secondary">
                                <small class="text-muted d-block">Progreso</small>
                                <span class="h5 fw-bold text-success"><?php echo $porcentaje; ?>%</span>
                            </div>
                        </div>
                    </div>

                    <div class="progress mb-4" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: <?php echo $porcentaje; ?>%"></div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mb-4 download-buttons">
                        <a href="../src/Controllers/exportar_excel.php" class="btn btn-outline-success btn-sm">
                            <i class="fa-solid fa-file-excel me-1"></i> Excel
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-danger btn-sm">
                            <i class="fa-solid fa-file-pdf me-1"></i> PDF
                        </button>
                    </div>
                    
                    <form action="../src/Controllers/agregar.php" method="POST" class="mb-4 bg-body-secondary p-3 rounded border">
                        <div class="row g-2">
                            <div class="col-md-12">
                                <input type="text" name="titulo_tarea" class="form-control" placeholder="¿Qué hay que hacer?" required>
                            </div>
                            <div class="col-sm-6">
                                <select name="categoria_id" class="form-select">
                                    <?php
                                    $cats = $conexion->query("SELECT * FROM categorias");
                                    while($c = $cats->fetch()) {
                                        echo "<option value='{$c['id']}'>{$c['nombre']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <input type="date" name="fecha_vencimiento" class="form-control">
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="btn-group btn-group-sm mb-3 w-100">
                        <a href="index.php" class="btn btn-outline-secondary <?php echo !isset($_GET['filtro']) ? 'active' : ''; ?>">Todas</a>
                        <a href="index.php?filtro=pendientes" class="btn btn-outline-secondary <?php echo ($_GET['filtro']??'') == 'pendientes' ? 'active' : ''; ?>">Pendientes</a>
                        <a href="index.php?filtro=completadas" class="btn btn-outline-secondary <?php echo ($_GET['filtro']??'') == 'completadas' ? 'active' : ''; ?>">Hechas</a>
                    </div>

                    <ul class="list-group list-group-flush">
                        <?php
                        $filtro = $_GET['filtro'] ?? 'todas';
                        $condicion = "1=1";
                        if ($filtro == 'pendientes') $condicion = "completada = 0";
                        if ($filtro == 'completadas') $condicion = "completada = 1";
                        
                        $sql = "SELECT t.*, c.nombre AS categoria_nombre 
                                FROM tareas t 
                                LEFT JOIN categorias c ON t.categoria_id = c.id 
                                WHERE $condicion ORDER BY t.id DESC";
                        
                        $sentencia = $conexion->query($sql);
                        $hayTareas = false;
                        $hoy = date('Y-m-d');

                        while ($fila = $sentencia->fetch()) {
                            $hayTareas = true;
                            $vence = $fila['fecha_vencimiento'];
                            $claseVencida = (!$fila['completada'] && $vence && $vence < $hoy) ? "text-danger fw-bold" : "text-muted";
                            ?>
                            <li class="list-group-item px-0 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="../src/Controllers/completar.php?id=<?php echo $fila['id']; ?>" 
                                           class="btn btn-sm <?php echo $fila['completada'] ? 'btn-success' : 'btn-outline-secondary'; ?> rounded-circle">
                                            <i class="fa-solid fa-check"></i>
                                        </a>
                                        <div>
                                            <span class="badge text-bg-secondary border me-1 small"><?php echo $fila['categoria_nombre']; ?></span>
                                            <span class="<?php echo $fila['completada'] ? 'text-decoration-line-through text-muted' : ''; ?>">
                                                <?php echo htmlspecialchars($fila['tarea']); ?>
                                            </span>
                                            <?php if($vence): ?>
                                                <br><small class="<?php echo $claseVencida; ?>">
                                                    <i class="fa-regular fa-calendar me-1"></i>Vence: <?php echo date('d/m/Y', strtotime($vence)); ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <a href="../src/Controllers/eliminar.php?id=<?php echo $fila['id']; ?>" class="text-danger ms-2">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </li>
                        <?php } ?>

                        <?php if (!$hayTareas): ?>
                            <li class="list-group-item text-center py-4 text-muted">
                                <i class="fa-solid fa-ghost fa-2x mb-2 d-block"></i>
                                No hay tareas aquí.
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="text-center mt-5 pb-4">
    <p class="text-secondary small">
        Developed with <i class="fa-solid fa-heart text-danger"></i> by 
        <span class="fw-bold">Magali Medina</span> &copy; 2026
    </p>
    <div class="d-flex justify-content-center gap-3">
        <a href="https://github.com/magaliamedina" class="text-muted"><i class="fa-brands fa-github fa-lg"></i></a>
        <a href="https://www.linkedin.com/in/magali-anabel-medina/" class="text-muted"><i class="fa-brands fa-linkedin fa-lg"></i></a>
    </div>
</footer>

<script>
(function () {
    var root = document.documentElement;
    var KEY = 'demo_theme';
    var btn = document.getElementById('themeToggle');
    var iconMoon = document.getElementById('themeIconMoon');
    var iconSun = document.getElementById('themeIconSun');
    if (!btn || !iconMoon || !iconSun) return;

    function syncIcons() {
        var dark = root.getAttribute('data-bs-theme') === 'dark';
        iconMoon.classList.toggle('d-none', !dark);
        iconSun.classList.toggle('d-none', dark);
        btn.setAttribute('title', dark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
    }

    syncIcons();

    btn.addEventListener('click', function () {
        var next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        root.setAttribute('data-bs-theme', next);
        localStorage.setItem(KEY, next);
        syncIcons();
    });
})();
</script>

</body>
</html>