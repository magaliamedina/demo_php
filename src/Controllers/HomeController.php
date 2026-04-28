<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\TaskRepository;

/**
 * Página principal: prepara datos y carga la vista (no contiene HTML largo).
 */
final class HomeController
{
    public function __construct(private TaskRepository $repository)
    {
    }

    /**
     * @param array<string, string> $query Normalmente $_GET
     */
    public function render(array $query): void
    {
        $filtroRaw = $query['filtro'] ?? 'todas';
        $filtro = in_array($filtroRaw, ['todas', 'pendientes', 'completadas'], true)
            ? $filtroRaw
            : 'todas';

        // "Todas" activa solo si no hay ?filtro= en la URL
        $todasActivas = !array_key_exists('filtro', $query);

        $stats = $this->repository->getDashboardStats();
        $categorias = $this->repository->getCategorias();
        $tareas = $this->repository->getTareasByFiltro($filtro);

        // Rutas relativas desde public/index.php hacia tus scripts de acción
        $urlCtrl = '../src/Controllers/';

        require BASE_PATH . '/views/home.php';
    }
}
