<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Representa una venta registrada.
 */
final class Venta
{
    /**
     * Construye una venta con sus datos tipados.
     *
     * @param int $id Identificador de la venta; 0 si aún no se ha guardado.
     * @param int $productoId Identificador del producto vendido.
     * @param int $cantidad Unidades vendidas.
     * @param float $precioUnitario Precio vigente al momento de vender.
     * @param float $total Importe total de la venta.
     * @param string $fechaHora Fecha y hora de registro.
     */
    public function __construct(
        public readonly int $id,
        public readonly int $productoId,
        public readonly int $cantidad,
        public readonly float $precioUnitario,
        public readonly float $total,
        public readonly string $fechaHora
    ) {
    }
}
