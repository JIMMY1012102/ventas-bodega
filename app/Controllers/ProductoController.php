<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Exceptions\AppException;
use App\Services\ProductoService;
use App\Support\Csrf;

/** Atiende las pantallas y acciones de productos. */
final class ProductoController
{
    /** @param ProductoService $productoService Servicio de productos. */
    public function __construct(private ProductoService $productoService) {}

    /** Muestra el formulario de registro. */
    public function mostrarFormulario(): void
    {
        $mensaje = $_SESSION['mensaje'] ?? null;
        unset($_SESSION['mensaje']);
        require dirname(__DIR__, 2) . '/views/productos/formulario.php';
    }

    /** Valida CSRF, delega el registro y redirige. */
    public function registrar(): void
    {
        if (!Csrf::verify(isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : null)) {
            $mensaje = 'La solicitud no es válida.';
            require dirname(__DIR__, 2) . '/views/productos/formulario.php';
            return;
        }
        try {
            $this->productoService->registrar($_POST);
            $_SESSION['mensaje'] = 'Producto registrado correctamente.';
            header('Location: ?route=productos/listar');
            exit;
        } catch (AppException | \InvalidArgumentException $exception) {
            $mensaje = $exception->getMessage();
            require dirname(__DIR__, 2) . '/views/productos/formulario.php';
        }
    }

    /** Muestra todos los productos. */
    public function listar(): void
    {
        $productos = $this->productoService->listar();
        require dirname(__DIR__, 2) . '/views/productos/lista.php';
    }
}

