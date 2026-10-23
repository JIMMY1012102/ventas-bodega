<?php
declare(strict_types=1);
$title = 'Ventas del día';
$content = static function () use ($ventas, $total): void {
    $e = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    echo '<h2>Ventas del día</h2>';
    if ($ventas === []) { echo '<p>No hay ventas registradas.</p>'; }
    else {
        echo '<table><tr><th>Producto ID</th><th>Cantidad</th><th>Precio</th><th>Total</th><th>Fecha</th></tr>';
        foreach ($ventas as $venta) {
            echo '<tr><td>' . $e((string) $venta->productoId) . '</td><td>' . $e((string) $venta->cantidad)
                . '</td><td>' . $e(number_format($venta->precioUnitario, 2)) . '</td><td>' . $e(number_format($venta->total, 2))
                . '</td><td>' . $e($venta->fechaHora) . '</td></tr>';
        }
        echo '</table>';
    }
    echo '<p><strong>Total vendido: ' . $e(number_format($total, 2)) . '</strong></p>';
};
require __DIR__ . '/../layout.php';

