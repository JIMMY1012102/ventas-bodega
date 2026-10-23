<?php
declare(strict_types=1);
namespace App\Services;
use App\Exceptions\AppException;
use App\Exceptions\CantidadNoPositivaException;
use App\Exceptions\DatabaseException;
use App\Exceptions\PrecioNegativoException;
use App\Exceptions\ProductoNoEncontradoException;
use App\Exceptions\StockInsuficienteException;
use App\Models\Venta;
use App\Repositories\ProductoRepositoryInterface;
use App\Repositories\VentaRepositoryInterface;
use App\Support\TransactionManagerInterface;

/** Ejecuta las reglas y la transacción atómica de ventas. */
final class VentaService
{
    /** Inyecta transacción y repositorios abstractos. */
    public function __construct(
        private TransactionManagerInterface $transaction,
        private VentaRepositoryInterface $ventaRepository,
        private ProductoRepositoryInterface $productoRepository
    ) {}

    /** Registra una venta y descuenta stock en una única transacción. */
    public function registrar(int $productoId, int $cantidad): Venta
    {
        try {
            $this->transaction->beginTransaction();
            $producto = $this->productoRepository->findById($productoId);
            if ($producto === null) {
                throw new ProductoNoEncontradoException('El producto no existe');
            }
            if ($cantidad <= 0) {
                throw new CantidadNoPositivaException('La cantidad debe ser mayor que cero');
            }
            if ($cantidad > $producto->stockActual) {
                throw new StockInsuficienteException('No hay stock suficiente');
            }
            if ($producto->precio < 0) {
                throw new PrecioNegativoException('El precio no es válido');
            }
            $venta = new Venta(
                0, $producto->id, $cantidad, $producto->precio,
                $producto->precio * $cantidad, date('Y-m-d H:i:s')
            );
            $venta = $this->ventaRepository->save($venta);
            if (!$this->productoRepository->updateStock($producto->id, $producto->stockActual - $cantidad)) {
                throw new DatabaseException('No se pudo actualizar el stock');
            }
            $this->transaction->commit();
            return $venta;
        } catch (\Throwable $exception) {
            $this->transaction->rollBack();
            if ($exception instanceof AppException) {
                throw $exception;
            }
            throw new DatabaseException('Error al registrar la venta', 0, $exception);
        }
    }

    /** Consulta ventas y total de una fecha. @return array{ventas: array<Venta>, total: float} */
    public function consultarDia(string $fecha): array
    {
        return [
            'ventas' => $this->ventaRepository->findByFecha($fecha),
            'total' => $this->ventaRepository->getTotalByFecha($fecha),
        ];
    }
}

