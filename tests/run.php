<?php
declare(strict_types=1);

use App\Exceptions\CantidadNoPositivaException;
use App\Exceptions\PrecioNegativoException;
use App\Exceptions\ProductoNoEncontradoException;
use App\Exceptions\StockInsuficienteException;
use App\Models\Producto;
use App\Models\Venta;
use App\Repositories\ProductoRepositoryInterface;
use App\Repositories\VentaRepositoryInterface;
use App\Services\ProductoService;
use App\Services\VentaService;
use App\Support\TransactionManagerInterface;

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (str_starts_with($class, $prefix)) {
        $path = dirname(__DIR__) . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_readable($path)) { require_once $path; }
    }
});

/** Repositorio falso de productos para pruebas unitarias. */
final class ProductoRepositoryFake implements ProductoRepositoryInterface
{
    /** @var array<int,Producto> */
    private array $productos;
    private int $nextId = 1;
    /** @param array<Producto> $productos */
    public function __construct(array $productos = [])
    {
        foreach ($productos as $producto) { $this->productos[$producto->id ?: $this->nextId] = new Producto($producto->id ?: $this->nextId, $producto->codigo, $producto->nombre, $producto->categoria, $producto->precio, $producto->stockActual, $producto->stockMinimo); $this->nextId++; }
    }
    /** Guarda en memoria. */
    public function save(Producto $producto): Producto { $saved = new Producto($this->nextId++, $producto->codigo, $producto->nombre, $producto->categoria, $producto->precio, $producto->stockActual, $producto->stockMinimo); $this->productos[$saved->id] = $saved; return $saved; }
    /** Busca en memoria. */
    public function findById(int $id): ?Producto { return $this->productos[$id] ?? null; }
    /** Busca por código. */
    public function findByCodigo(string $codigo): ?Producto { foreach ($this->productos as $p) { if ($p->codigo === $codigo) return $p; } return null; }
    /** Lista en memoria. @return array<Producto> */
    public function findAll(): array { return array_values($this->productos); }
    /** Comprueba código. */
    public function existsByCodigo(string $codigo): bool { return $this->findByCodigo($codigo) !== null; }
    /** Actualiza stock. */
    public function updateStock(int $id, int $nuevoStock): bool { if (!isset($this->productos[$id])) return false; $p = $this->productos[$id]; $this->productos[$id] = new Producto($p->id, $p->codigo, $p->nombre, $p->categoria, $p->precio, $nuevoStock, $p->stockMinimo); return true; }
}

/** Repositorio falso de ventas para pruebas unitarias. */
final class VentaRepositoryFake implements VentaRepositoryInterface
{
    /** @var array<int,Venta> */
    private array $ventas = [];
    private int $nextId = 1;
    /** Guarda en memoria. */
    public function save(Venta $venta): Venta { $saved = new Venta($this->nextId++, $venta->productoId, $venta->cantidad, $venta->precioUnitario, $venta->total, $venta->fechaHora); $this->ventas[$saved->id] = $saved; return $saved; }
    /** Filtra por fecha. @return array<Venta> */
    public function findByFecha(string $fecha): array { return array_values(array_filter($this->ventas, static fn (Venta $v): bool => substr($v->fechaHora, 0, 10) === $fecha)); }
    /** Suma por fecha. */
    public function getTotalByFecha(string $fecha): float { return array_sum(array_map(static fn (Venta $v): float => $v->total, $this->findByFecha($fecha))); }
}

/** Doble de transacción para pruebas sin motor de BD. */
final class TransactionManagerFake implements TransactionManagerInterface
{
    public int $begins = 0;
    public int $commits = 0;
    public int $rollbacks = 0;
    /** Inicia prueba. */
    public function beginTransaction(): void { $this->begins++; }
    /** Confirma prueba. */
    public function commit(): void { $this->commits++; }
    /** Revierte prueba. */
    public function rollBack(): void { $this->rollbacks++; }
}

/** Ejecuta una prueba y muestra su resultado. */
function test(string $name, callable $test): bool
{
    try { $test(); echo "[OK] {$name}\n"; return true; }
    catch (Throwable $exception) { echo "[FAIL] {$name}: {$exception->getMessage()}\n"; return false; }
}
/** Comprueba una condición. */
function assertTrue(bool $condition, string $message): void { if (!$condition) throw new RuntimeException($message); }

$validProduct = static fn (float $price = 10.0, int $stock = 5): Producto => new Producto(1, 'A1', 'Arroz', 'Abarrotes', $price, $stock, 2);
$passed = 0;
$passed += test('P1 Venta válida', function () use ($validProduct): void { $p = new ProductoRepositoryFake([$validProduct()]); $v = new VentaRepositoryFake(); $t = new TransactionManagerFake(); $sale = (new VentaService($t, $v, $p))->registrar(1, 3); assertTrue($sale->total === 30.0, 'total incorrecto'); assertTrue($p->findById(1)?->stockActual === 2, 'stock incorrecto'); assertTrue($t->commits === 1, 'no confirmó'); });
$passed += test('P2 Cantidad no positiva', function () use ($validProduct): void { try { (new VentaService(new TransactionManagerFake(), new VentaRepositoryFake(), new ProductoRepositoryFake([$validProduct()])))->registrar(1, 0); throw new RuntimeException('no lanzó excepción'); } catch (CantidadNoPositivaException) {} });
$passed += test('P3 Stock insuficiente sin cambios', function () use ($validProduct): void { $p = new ProductoRepositoryFake([$validProduct(10, 2)]); try { (new VentaService(new TransactionManagerFake(), new VentaRepositoryFake(), $p))->registrar(1, 5); throw new RuntimeException('no lanzó excepción'); } catch (StockInsuficienteException) { assertTrue($p->findById(1)?->stockActual === 2, 'stock modificado'); } });
$passed += test('P4 Producto inexistente', function (): void { try { (new VentaService(new TransactionManagerFake(), new VentaRepositoryFake(), new ProductoRepositoryFake()))->registrar(999, 1); throw new RuntimeException('no lanzó excepción'); } catch (ProductoNoEncontradoException) {} });
$passed += test('P5 Precio negativo', function () use ($validProduct): void { try { (new VentaService(new TransactionManagerFake(), new VentaRepositoryFake(), new ProductoRepositoryFake([$validProduct(-5)])))->registrar(1, 1); throw new RuntimeException('no lanzó excepción'); } catch (PrecioNegativoException) {} });
$passed += test('P6 Stock bajo', function (): void { $repo = new ProductoRepositoryFake([new Producto(1, 'A', 'Bajo', 'C', 1, 1, 3), new Producto(2, 'B', 'Normal', 'C', 1, 5, 2)]); $low = (new ProductoService($repo))->listarConStockBajo(); assertTrue(count($low) === 1 && $low[0]->id === 1, 'filtro incorrecto'); });
echo "{$passed}/6 pruebas aprobadas\n";
exit($passed === 6 ? 0 : 1);
