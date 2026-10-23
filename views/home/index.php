<?php
declare(strict_types=1);
$title = 'Inicio';
$content = static function (): void {
    echo '<h2>Panel principal</h2><p>Seleccione una operación.</p>';
};
require __DIR__ . '/../layout.php';

