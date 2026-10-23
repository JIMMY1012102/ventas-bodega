<?php
declare(strict_types=1);
namespace App\Repositories;
use App\Exceptions\DatabaseException;
use App\Models\Venta;
use PDO;
use PDOException;

/** Acceso PDO a la tabla venta. */
final class VentaRepository implements VentaRepositoryInterface
{
    /** @param PDO $pdo Conexión PDO activa. */
    public function __construct(private PDO $pdo) {}

    /** Guarda una venta con sentencia preparada. */
    public function save(Venta $venta): Venta
    {
        try {
            $statement = $this->pdo->prepare(
                'INSERT INTO venta (producto_id, cantidad, precio_unitario, total, fecha_hora) '
                . 'VALUES (:producto_id, :cantidad, :precio_unitario, :total, :fecha_hora)'
            );
            $statement->execute([
                ':producto_id' => $venta->productoId,
                ':cantidad' => $venta->cantidad,
                ':precio_unitario' => $venta->precioUnitario,
                ':total' => $venta->total,
                ':fecha_hora' => $venta->fechaHora,
            ]);
            return new Venta(
                (int) $this->pdo->lastInsertId(), $venta->productoId, $venta->cantidad,
                $venta->precioUnitario, $venta->total, $venta->fechaHora
            );
        } catch (PDOException $exception) {
            throw new DatabaseException('No se pudo guardar la venta.', 0, $exception);
        }
    }

    /** Devuelve las ventas del día solicitado. @return array<Venta> */
    public function findByFecha(string $fecha): array
    {
        try {
            $statement = $this->pdo->prepare(
                'SELECT id, producto_id, cantidad, precio_unitario, total, fecha_hora '
                . 'FROM venta WHERE DATE(fecha_hora) = :fecha ORDER BY fecha_hora DESC'
            );
            $statement->execute([':fecha' => $fecha]);
            return array_map(fn (array $row): Venta => $this->mapRow($row), $statement->fetchAll());
        } catch (PDOException $exception) {
            throw new DatabaseException('No se pudieron consultar las ventas.', 0, $exception);
        }
    }

    /** Suma las ventas de una fecha. */
    public function getTotalByFecha(string $fecha): float
    {
        try {
            $statement = $this->pdo->prepare(
                'SELECT COALESCE(SUM(total), 0) FROM venta WHERE DATE(fecha_hora) = :fecha'
            );
            $statement->execute([':fecha' => $fecha]);
            return (float) $statement->fetchColumn();
        } catch (PDOException $exception) {
            throw new DatabaseException('No se pudo calcular el total.', 0, $exception);
        }
    }

    /** Convierte una fila de BD en un modelo tipado. @param array<string,mixed> $row */
    private function mapRow(array $row): Venta
    {
        return new Venta(
            (int) $row['id'], (int) $row['producto_id'], (int) $row['cantidad'],
            (float) $row['precio_unitario'], (float) $row['total'], (string) $row['fecha_hora']
        );
    }
}

