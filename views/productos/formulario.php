<?php
declare(strict_types=1);
$title = 'Registrar producto';
$content = static function () use ($mensaje): void {
    $e = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    echo '<h2>Registrar producto</h2>';
    if (isset($mensaje) && is_string($mensaje)) { echo '<p role="alert">' . $e($mensaje) . '</p>'; }
    echo '<form method="post" action="?route=productos/guardar">';
    echo '<input type="hidden" name="csrf_token" value="' . $e(\App\Support\Csrf::token()) . '">';
    echo '<label>Código <input name="codigo" required></label><br>';
    echo '<label>Nombre <input name="nombre" required></label><br>';
    echo '<label>Categoría <input name="categoria" required></label><br>';
    echo '<label>Precio <input type="number" name="precio" min="0" step="0.01" required></label><br>';
    echo '<label>Stock actual <input type="number" name="stock_actual" min="0" required></label><br>';
    echo '<label>Stock mínimo <input type="number" name="stock_minimo" min="0" required></label><br>';
    echo '<button type="submit">Guardar</button></form>';
};
require __DIR__ . '/../layout.php';

