<?php
declare(strict_types=1);
$title = 'Productos';
$content = static function () use ($productos): void {
    $e = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    echo '<h2>Productos</h2>';
    if ($productos === []) { echo '<p>No hay productos registrados.</p>'; return; }
    echo '<table><tr><th>Código</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Mínimo</th></tr>';
    foreach ($productos as $producto) {
        $class = $producto->stockActual <= $producto->stockMinimo ? ' style="background:#ffd6d6"' : '';
        echo '<tr' . $class . '><td>' . $e($producto->codigo) . '</td><td>' . $e($producto->nombre)
            . '</td><td>' . $e($producto->categoria) . '</td><td>' . $e(number_format($producto->precio, 2))
            . '</td><td>' . $e((string) $producto->stockActual) . '</td><td>' . $e((string) $producto->stockMinimo) . '</td></tr>';
    }
    echo '</table>';
};
require __DIR__ . '/../layout.php';

