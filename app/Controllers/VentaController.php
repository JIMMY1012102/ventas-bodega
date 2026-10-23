<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Exceptions\AppException;
use App\Services\ProductoService;
use App\Services\VentaService;
use App\Support\Csrf;

/** Atiende el registro y consulta de ventas. */
final class VentaController
{
    /** Inyecta venta y catálogo sin duplicar reglas de negocio. */
    public function __construct(
        private VentaService $ventaService,
        private ProductoService $productoService
    ) {}

    /** Muestra el formulario usando ProductoService::listar(). */
    public function mostrarFormulario(): void
    {
        $productos = $this->productoService->listar();
        $mensaje = $_SESSION['mensaje'] ?? null;
        unset($_SESSION['mensaje']);
        require dirname(__DIR__, 2) . '/views/ventas/formulario.php';
    }

    /** Valida CSRF y delega el registro atómico al servicio. */
    public function registrar(): void
    {
        if (!Csrf::verify(isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : null)) {
            $mensaje = 'La solicitud no es válida.';
            $productos = $this->productoService->listar();
            require dirname(__DIR__, 2) . '/views/ventas/formulario.php';
            return;
        }
        try {
            $productoId = (int) ($_POST['producto_id'] ?? 0);
            $cantidad = (int) ($_POST['cantidad'] ?? 0);
            $this->ventaService->registrar($productoId, $cantidad);
            $_SESSION['mensaje'] = 'Venta registrada correctamente.';
            header('Location: ?route=ventas/registrar');
            exit;
        } catch (AppException $exception) {
            $mensaje = $exception->getMessage();
            $productos = $this->productoService->listar();
            require dirname(__DIR__, 2) . '/views/ventas/formulario.php';
        }
    }

    /** Consulta las ventas de la fecha actual. */
    public function consultarDia(): void
    {
        $reporte = $this->ventaService->consultarDia(date('Y-m-d'));
        $ventas = $reporte['ventas'];
        $total = $reporte['total'];
        require dirname(__DIR__, 2) . '/views/ventas/dia.php';
    }
}

