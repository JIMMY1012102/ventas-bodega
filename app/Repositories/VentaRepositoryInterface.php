<?php
declare(strict_types=1);
namespace App\Repositories;
use App\Models\Venta;

/** Contrato de persistencia de ventas. */
interface VentaRepositoryInterface
{
    /** Guarda una venta. */
    public function save(Venta $venta): Venta;
    /** Devuelve las ventas de una fecha. @return array<Venta> */
    public function findByFecha(string $fecha): array;
    /** Devuelve el total vendido en una fecha. */
    public function getTotalByFecha(string $fecha): float;
}

