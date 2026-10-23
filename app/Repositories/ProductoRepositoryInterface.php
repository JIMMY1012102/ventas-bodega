<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Producto;

/**
 * Define las operaciones de persistencia disponibles para productos.
 */
interface ProductoRepositoryInterface
{
    /**
     * Guarda un producto y devuelve su representación persistida.
     *
     * @param Producto $producto Producto que se desea guardar.
     * @return Producto Producto con su identificador asignado si corresponde.
     */
    public function save(Producto $producto): Producto;

    /**
     * Busca un producto por su identificador.
     *
     * La implementación PDO utilizará bloqueo de fila cuando la operación
     * se ejecute dentro de una transacción de venta.
     *
     * @param int $id Identificador del producto.
     * @return Producto|null Producto encontrado o null si no existe.
     */
    public function findById(int $id): ?Producto;

    /**
     * Busca un producto por su código único.
     *
     * @param string $codigo Código del producto.
     * @return Producto|null Producto encontrado o null si no existe.
     */
    public function findByCodigo(string $codigo): ?Producto;

    /**
     * Devuelve todos los productos registrados.
     *
     * @return array<Producto> Productos ordenados por nombre.
     */
    public function findAll(): array;

    /**
     * Comprueba si existe un producto con el código indicado.
     *
     * @param string $codigo Código que se desea comprobar.
     * @return bool true si el código ya existe.
     */
    public function existsByCodigo(string $codigo): bool;

    /**
     * Actualiza exclusivamente el stock actual de un producto.
     *
     * @param int $id Identificador del producto.
     * @param int $nuevoStock Nuevo stock calculado por el servicio.
     * @return bool true si se actualizó una fila.
     */
    public function updateStock(int $id, int $nuevoStock): bool;
}
