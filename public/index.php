<?php
declare(strict_types=1);

use function App\Config\createPdoConnection;
use App\Controllers\HomeController;
use App\Controllers\ProductoController;
use App\Controllers\VentaController;
use App\Repositories\ProductoRepository;
use App\Repositories\VentaRepository;
use App\Services\ProductoService;
use App\Services\VentaService;
use App\Support\PdoTransactionManager;

require_once dirname(__DIR__) . '/config/database.php';
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) { return; }
    $path = dirname(__DIR__) . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_readable($path)) { require_once $path; }
});
session_start();

try {
    $pdo = createPdoConnection();
    $productoRepository = new ProductoRepository($pdo);
    $ventaRepository = new VentaRepository($pdo);
    $productoService = new ProductoService($productoRepository);
    $ventaService = new VentaService(new PdoTransactionManager($pdo), $ventaRepository, $productoRepository);
    $route = (string) ($_GET['route'] ?? 'home/index');
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $controller = match ($route) {
        'home/index' => new HomeController(),
        'productos/registrar' => new ProductoController($productoService),
        'productos/guardar' => new ProductoController($productoService),
        'productos/listar' => new ProductoController($productoService),
        'ventas/registrar' => new VentaController($ventaService, $productoService),
        'ventas/guardar' => new VentaController($ventaService, $productoService),
        'ventas/dia' => new VentaController($ventaService, $productoService),
        default => new HomeController(),
    };
    match ($route) {
        'home/index' => $controller->index(),
        'productos/registrar' => $controller->mostrarFormulario(),
        'productos/guardar' => $method === 'POST' ? $controller->registrar() : $controller->mostrarFormulario(),
        'productos/listar' => $controller->listar(),
        'ventas/registrar' => $controller->mostrarFormulario(),
        'ventas/guardar' => $method === 'POST' ? $controller->registrar() : $controller->mostrarFormulario(),
        'ventas/dia' => $controller->consultarDia(),
        default => $controller->index(),
    };
} catch (\Throwable $exception) {
    http_response_code(500);
    echo 'No se pudo procesar la solicitud.';
}
