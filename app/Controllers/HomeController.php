<?php
declare(strict_types=1);
namespace App\Controllers;

/** Controlador de la pantalla principal. */
final class HomeController
{
    /** Muestra los accesos principales. */
    public function index(): void
    {
        require dirname(__DIR__, 2) . '/views/home/index.php';
    }
}

