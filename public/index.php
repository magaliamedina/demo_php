<?php

declare(strict_types=1);

/**
 * Punto de entrada web: solo arranca la app y delega en el controlador.
 * La lógica de negocio y consultas están en src/; el HTML en views/.
 */
require dirname(__DIR__) . '/bootstrap.php';
require BASE_PATH . '/src/helpers.php';

use App\Controllers\HomeController;
use App\Repositories\TaskRepository;

$repository = new TaskRepository($conexion);
$controller = new HomeController($repository);
$controller->render($_GET);
