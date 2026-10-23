<?php
declare(strict_types=1);
namespace App\Repositories;
use App\Exceptions\DatabaseException;
use App\Models\Producto;
use PDO;
use PDOException;

/** Acceso PDO a la tabla producto. */
final class ProductoRepository implements ProductoRepositoryInterface
{
    /** @param PDO $pdo Conexión PDO activa. */
    public function __construct(private PDO $pdo) {}

    /** Guarda un producto mediante una sentencia preparada. */
    public function save(Producto $producto): Producto
    {
        try {
            $statement = $this->pdo->prepare(
                'INSERT INTO producto (codigo, nombre, categoria, precio, stock_actual, stock_minimo) '
                . 'VALUES (:codigo, :nombre, :categoria, :precio, :stock_actual, :stock_minimo)'
            );
            $statement->execute([
                ':codigo' => $producto->codigo,
                ':nombre' => $producto->nombre,
                ':categoria' => $producto->categoria,
                ':precio' => $producto->precio,
                ':stock_actual' => $producto->stockActual,
                ':stock_minimo' => $producto->stockMinimo,
            ]);
            return new Producto(
                (int) $this->pdo->lastInsertId(), $producto->codigo, $producto->nombre,
                $producto->categoria, $producto->precio, $producto->stockActual, $producto->stockMinimo
            );
        } catch (PDOException $exception) {
            throw new DatabaseException('No se pudo guardar el producto.', 0, $exception);
        }
    }

    /** Busca un producto por ID y bloquea su fila dentro de una transacción. */
    public function findById(int $id): ?Producto
    {
        try {
            $statement = $this->pdo->prepare(
                'SELECT id, codigo, nombre, categoria, precio, stock_actual, stock_minimo '
                . 'FROM producto WHERE id = :id FOR UPDATE'
            );
            $statement->execute([':id' => $id]);
            $row = $statement->fetch();
            return $row === false ? null : $this->mapRow($row);
        } catch (PDOException $exception) {
            throw new DatabaseException('No se pudo consultar el producto.', 0, $exception);
        }
    }

    /** Busca un producto por código. */
    public function findByCodigo(string $codigo): ?Producto
    {
        try {
            $statement = $this->pdo->prepare(
                'SELECT id, codigo, nombre, categoria, precio, stock_actual, stock_minimo '
                . 'FROM producto WHERE codigo = :codigo'
            );
            $statement->execute([':codigo' => $codigo]);
            $row = $statement->fetch();
            return $row === false ? null : $this->mapRow($row);
        } catch (PDOException $exception) {
            throw new DatabaseException('No se pudo consultar el producto.', 0, $exception);
        }
    }

    /** Devuelve todos los productos ordenados por nombre. @return array<Producto> */
    public function findAll(): array
    {
        try {
            $statement = $this->pdo->query(
                'SELECT id, codigo, nombre, categoria, precio, stock_actual, stock_minimo '
                . 'FROM producto ORDER BY nombre'
            );
            return array_map(fn (array $row): Producto => $this->mapRow($row), $statement->fetchAll());
        } catch (PDOException $exception) {
            throw new DatabaseException('No se pudo listar los productos.', 0, $exception);
        }
    }

    /** Comprueba si existe un código. */
    public function existsByCodigo(string $codigo): bool
    {
        try {
            $statement = $this->pdo->prepare('SELECT COUNT(*) FROM producto WHERE codigo = :codigo');
            $statement->execute([':codigo' => $codigo]);
            return (int) $statement->fetchColumn() > 0;
        } catch (PDOException $exception) {
            throw new DatabaseException('No se pudo verificar el código.', 0, $exception);
        }
    }

    /** Actualiza únicamente el stock actual. */
    public function updateStock(int $id, int $nuevoStock): bool
    {
        try {
            $statement = $this->pdo->prepare(
                'UPDATE producto SET stock_actual = :stock WHERE id = :id'
            );
            $statement->execute([':stock' => $nuevoStock, ':id' => $id]);
            return $statement->rowCount() === 1;
        } catch (PDOException $exception) {
            throw new DatabaseException('No se pudo actualizar el stock.', 0, $exception);
        }
    }

    /** Convierte una fila de BD en un modelo tipado. @param array<string,mixed> $row */
    private function mapRow(array $row): Producto
    {
        return new Producto(
            (int) $row['id'], (string) $row['codigo'], (string) $row['nombre'],
            (string) $row['categoria'], (float) $row['precio'], (int) $row['stock_actual'],
            (int) $row['stock_minimo']
        );
    }
}

