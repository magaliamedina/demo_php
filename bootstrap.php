<?php

declare(strict_types=1);

/**
 * Arranque de la aplicación: constantes, conexión a BD y autoload simple (sin Composer).
 * Debe vivir en la raíz del proyecto (junto a config/, src/, public/), nunca dentro de public/css.
 */
define('BASE_PATH', __DIR__);

require BASE_PATH . '/config/db.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/src/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require $file;
    }
});
