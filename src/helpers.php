<?php

declare(strict_types=1);

/**
 * Escapa texto para HTML (evita XSS al mostrar datos de la BD).
 */
function e(?string $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}
