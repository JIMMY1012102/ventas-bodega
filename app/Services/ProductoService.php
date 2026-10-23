<?php
declare(strict_types=1);
namespace App\Services;
use App\Exceptions\CodigoDuplicadoException;
use App\Exceptions\PrecioNegativoException;
use App\Models\Producto;
use App\Repositories\ProductoRepositoryInterface;

/** Contiene las reglas de negocio de productos. */
final class ProductoService
{
    /** @param ProductoRepositoryInterface $productoRepository Persistencia de productos. */
    public function __construct(private ProductoRepositoryInterface $productoRepository) {}

    /** Valida y registra un producto. @param array<string,mixed> $datos */
    public function registrar(array $datos): Producto
    {
        $codigo = trim((string) ($datos['codigo'] ?? ''));
        $nombre = trim((string) ($datos['nombre'] ?? ''));
        $categoria = trim((string) ($datos['categoria'] ?? ''));
        $precio = (float) ($datos['precio'] ?? -1);
        $stockActual = (int) ($datos['stock_actual'] ?? -1);
        $stockMinimo = (int) ($datos['stock_minimo'] ?? -1);
        if ($this->productoRepository->existsByCodigo($codigo)) {
            throw new CodigoDuplicadoException('El código ya está registrado');
        }
        if ($precio < 0) {
            throw new PrecioNegativoException('El precio no puede ser negativo');
        }
        if ($stockActual < 0 || $stockMinimo < 0) {
            throw new \InvalidArgumentException('El stock no puede ser negativo');
        }
        return $this->productoRepository->save(
            new Producto(0, $codigo, $nombre, $categoria, $precio, $stockActual, $stockMinimo)
        );
    }

    /** Devuelve todos los productos. @return array<Producto> */
    public function listar(): array { return $this->productoRepository->findAll(); }

    /** Devuelve solo productos con stock bajo. @return array<Producto> */
    public function listarConStockBajo(): array
    {
        return array_values(array_filter(
            $this->productoRepository->findAll(),
            fn (Producto $producto): bool => $producto->stockActual <= $producto->stockMinimo
        ));
    }
}

