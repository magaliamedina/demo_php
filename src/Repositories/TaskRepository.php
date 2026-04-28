<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

/**
 * Todas las consultas relacionadas con tareas y categorías viven aquí.
 * Si cambia la BD, normalmente solo se toca esta clase.
 */
final class TaskRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @return array{total:int, completadas:int, vencidas:int, porcentaje:int}
     */
    public function getDashboardStats(): array
    {
        $total = (int) $this->pdo->query('SELECT COUNT(*) FROM tareas')->fetchColumn();
        $completadas = (int) $this->pdo->query('SELECT COUNT(*) FROM tareas WHERE completada = 1')->fetchColumn();
        $vencidas = (int) $this->pdo->query(
            'SELECT COUNT(*) FROM tareas WHERE completada = 0 AND fecha_vencimiento < CURDATE()'
        )->fetchColumn();
        $porcentaje = $total > 0 ? (int) round(($completadas / $total) * 100) : 0;

        return [
            'total' => $total,
            'completadas' => $completadas,
            'vencidas' => $vencidas,
            'porcentaje' => $porcentaje,
        ];
    }

    /**
     * @return list<array{id:int|string, nombre:string}>
     */
    public function getCategorias(): array
    {
        $stmt = $this->pdo->query('SELECT id, nombre FROM categorias ORDER BY id');

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getTareasByFiltro(string $filtro): array
    {
        $cond = match ($filtro) {
            'pendientes' => 'completada = 0',
            'completadas' => 'completada = 1',
            default => '1=1',
        };

        $sql = 'SELECT t.*, c.nombre AS cat
                FROM tareas t
                LEFT JOIN categorias c ON t.categoria_id = c.id
                WHERE ' . $cond . '
                ORDER BY t.id DESC';

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
