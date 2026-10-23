<?php
declare(strict_types=1);
/** @var string $title */
/** @var callable $content */
?><!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title></head>
<body>
<header><h1>Minimarket La Esquina</h1><nav><a href="?route=home/index">Inicio</a> | <a href="?route=productos/listar">Productos</a> | <a href="?route=productos/registrar">Registrar producto</a> | <a href="?route=ventas/registrar">Registrar venta</a> | <a href="?route=ventas/dia">Ventas del día</a></nav></header>
<main><?php $content(); ?></main>
</body>
</html>

