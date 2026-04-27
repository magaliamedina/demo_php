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
        .card { border-radius: 15px; border: none; }
        .list-group-item { border-left: none; border-right: none; transition: 0.3s; background: transparent; }
        [data-bs-theme="light"] .list-group-item:hover { background-color: #f8f9fa; }
        [data-bs-theme="dark"] .list-group-item:hover { background-color: rgba(255, 255, 255, 0.03); }
        
        /* Ajuste para los modales en modo oscuro nativo de Bootstrap 5.3 */
        .modal-content { border-radius: 15px; }

        @media print {
            .btn, form, .btn-group, footer, .progress, .download-buttons, #themeToggle { display: none !important; }
            .card { box-shadow: none !important; border: 1px solid #ddd !important; }
            body { background-color: white !important; }
        }
    </style>
</head>
<body class="min-vh-100 bg-body">

<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-9 col-lg-7">
            <div class="card shadow-sm border">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="fs-3 fw-bold text-primary m-0">
                            <i class="fa-solid fa-clipboard-check me-2"></i>Gestor
                        </h2>
                        <button type="button" id="themeToggle" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="fa-solid fa-moon d-none" id="themeIconMoon"></i>
                            <i class="fa-solid fa-sun d-none" id="themeIconSun"></i>
                        </button>
                    </div>

                    <?php
                    $total_tareas = $conexion->query("SELECT COUNT(*) FROM tareas")->fetchColumn();
                    $completadas = $conexion->query("SELECT COUNT(*) FROM tareas WHERE completada = 1")->fetchColumn();
                    $vencidas = $conexion->query("SELECT COUNT(*) FROM tareas WHERE completada = 0 AND fecha_vencimiento < CURDATE()")->fetchColumn();
                    $porcentaje = ($total_tareas > 0) ? round(($completadas / $total_tareas) * 100) : 0;
                    ?>

                    <div class="row mb-3 text-center g-2">
                        <div class="col-4">
                            <div class="p-2 border rounded bg-body-tertiary">
                                <small class="text-muted d-block">Total</small>
                                <span class="h5 fw-bold"><?php echo $total_tareas; ?></span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 border rounded bg-body-tertiary">
                                <small class="text-muted d-block text-danger">Vencidas</small>
                                <span class="h5 fw-bold text-danger"><?php echo $vencidas; ?></span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 border rounded bg-body-tertiary">
                                <small class="text-muted d-block text-success">Progreso</small>
                                <span class="h5 fw-bold text-success"><?php echo $porcentaje; ?>%</span>
                            </div>
                        </div>
                    </div>

                    <div class="progress mb-4" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: <?php echo $porcentaje; ?>%"></div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mb-4">
                        <a href="../src/Controllers/exportar_excel.php" class="btn btn-outline-success btn-sm">
                            <i class="fa-solid fa-file-excel me-1"></i> Excel
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-danger btn-sm">
                            <i class="fa-solid fa-file-pdf me-1"></i> PDF
                        </button>
                    </div>
                    
                    <form action="../src/Controllers/agregar.php" method="POST" class="mb-4 bg-body-tertiary p-3 rounded border">
                        <div class="row g-2">
                            <div class="col-12">
                                <input type="text" name="titulo_tarea" class="form-control" placeholder="Nueva tarea..." required>
                            </div>
                            <div class="col-6">
                                <select name="categoria_id" class="form-select">
                                    <?php
                                    $cats = $conexion->query("SELECT * FROM categorias");
                                    while($c = $cats->fetch()) echo "<option value='{$c['id']}'>{$c['nombre']}</option>";
                                    ?>
                                </select>
                            </div>
                            <div class="col-4">
                                <input type="date" name="fecha_vencimiento" class="form-control">
                            </div>
                            <div class="col-2">
                                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-plus"></i></button>
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
                        $cond = ($filtro == 'pendientes') ? "completada = 0" : (($filtro == 'completadas') ? "completada = 1" : "1=1");
                        $stmt = $conexion->query("SELECT t.*, c.nombre AS cat FROM tareas t LEFT JOIN categorias c ON t.categoria_id = c.id WHERE $cond ORDER BY t.id DESC");
                        
                        $all_tasks = $stmt->fetchAll();
                        if (!$all_tasks) {
                            echo "<li class='list-group-item text-center py-4 text-muted'><i class='fa-solid fa-ghost fa-2x mb-2 d-block'></i>Vacío</li>";
                        } else {
                            foreach ($all_tasks as $fila) {
                                $vence = $fila['fecha_vencimiento'];
                                $vencida = (!$fila['completada'] && $vence && $vence < date('Y-m-d'));
                                ?>
                                <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3 text-truncate">
                                        <a href="../src/Controllers/completar.php?id=<?php echo $fila['id']; ?>" class="btn btn-sm <?php echo $fila['completada'] ? 'btn-success' : 'btn-outline-secondary'; ?> rounded-circle">
                                            <i class="fa-solid fa-check"></i>
                                        </a>
                                        <div class="text-truncate">
                                            <span class="badge text-bg-secondary border small mb-1"><?php echo $fila['cat']; ?></span>
                                            <div class="<?php echo $fila['completada'] ? 'text-decoration-line-through text-muted' : ''; ?> text-truncate">
                                                <?php echo htmlspecialchars($fila['tarea']); ?>
                                            </div>
                                            <?php if($vence): ?>
                                                <small class="<?php echo $vencida ? 'text-danger fw-bold' : 'text-muted'; ?>">
                                                    <i class="fa-regular fa-calendar me-1"></i><?php echo date('d/m/Y', strtotime($vence)); ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm text-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $fila['id']; ?>">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <a href="../src/Controllers/eliminar.php?id=<?php echo $fila['id']; ?>" class="btn btn-sm text-danger">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </div>
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php foreach ($all_tasks as $fila): ?>
<div class="modal fade" id="editModal<?php echo $fila['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-secondary">
            <form action="../src/Controllers/editar.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Editar Tarea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?php echo $fila['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Descripción</label>
                        <input type="text" name="tarea" class="form-control" value="<?php echo htmlspecialchars($fila['tarea']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Fecha Límite</label>
                        <input type="date" name="fecha_vencimiento" class="form-control" value="<?php echo $fila['fecha_vencimiento']; ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<footer class="text-center mt-5 pb-4">
    <p class="text-secondary small">Dev with <i class="fa-solid fa-heart text-danger"></i> by <b>Magali Medina</b> &copy; 2026</p>
    <div class="d-flex justify-content-center gap-3">
        <a href="https://github.com/magaliamedina" class="text-muted"><i class="fa-brands fa-github fa-lg"></i></a>
        <a href="https://www.linkedin.com/in/magali-anabel-medina/" class="text-muted"><i class="fa-brands fa-linkedin fa-lg"></i></a>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    const root = document.documentElement;
    const btn = document.getElementById('themeToggle');
    const moon = document.getElementById('themeIconMoon');
    const sun = document.getElementById('themeIconSun');

    function updateIcons() {
        const isDark = root.getAttribute('data-bs-theme') === 'dark';
        moon.classList.toggle('d-none', isDark);
        sun.classList.toggle('d-none', !isDark);
    }
    updateIcons();

    btn.addEventListener('click', () => {
        const theme = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        root.setAttribute('data-bs-theme', theme);
        localStorage.setItem('demo_theme', theme);
        updateIcons();
    });
})();
</script>
</body>
</html>