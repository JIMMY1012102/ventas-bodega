<?php
declare(strict_types=1);
$title = 'Registrar venta';
$content = static function () use ($productos, $mensaje): void {
    $e = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    echo '<h2>Registrar venta</h2>';
    if (isset($mensaje) && is_string($mensaje)) { echo '<p role="alert">' . $e($mensaje) . '</p>'; }
    if ($productos === []) { echo '<p>No hay productos registrados.</p>'; return; }
    echo '<form method="post" action="?route=ventas/guardar">';
    echo '<input type="hidden" name="csrf_token" value="' . $e(\App\Support\Csrf::token()) . '">';
    echo '<label>Producto <select name="producto_id" required>';
    foreach ($productos as $producto) {
        echo '<option value="' . $e((string) $producto->id) . '">' . $e($producto->nombre) . ' (' . $e($producto->codigo) . ')</option>';
    }
    echo '</select></label><br><label>Cantidad <input type="number" name="cantidad" min="1" required></label><br><button type="submit">Registrar venta</button></form>';
};
require __DIR__ . '/../layout.php';

