<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Representa un producto registrado en el inventario.
 *
 * El modelo solo transporta datos entre las capas de la aplicación.
 */
final class Producto
{
    /**
     * Construye un producto con sus datos tipados.
     *
     * @param int $id Identificador de la base de datos; 0 si aún no se ha guardado.
     * @param string $codigo Código único del producto.
     * @param string $nombre Nombre descriptivo del producto.
     * @param string $categoria Categoría del producto.
     * @param float $precio Precio unitario de venta.
     * @param int $stockActual Unidades disponibles actualmente.
     * @param int $stockMinimo Umbral para identificar stock bajo.
     */
    public function __construct(
        public readonly int $id,
        public readonly string $codigo,
        public readonly string $nombre,
        public readonly string $categoria,
        public readonly float $precio,
        public readonly int $stockActual,
        public readonly int $stockMinimo
    ) {
    }
}
