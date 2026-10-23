<?php
declare(strict_types=1);
namespace App\Support;

/** Genera y valida tokens CSRF almacenados en la sesión. */
final class Csrf
{
    /** Devuelve el token de sesión, generándolo si no existe. */
    public static function token(): string
    {
        if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /** Comprueba un token recibido contra el token de sesión. */
    public static function verify(?string $token): bool
    {
        return isset($_SESSION['csrf_token']) && is_string($_SESSION['csrf_token'])
            && $token !== null && hash_equals($_SESSION['csrf_token'], $token);
    }
}

