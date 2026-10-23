<?php

declare(strict_types=1);

namespace App\Config;

use PDO;
use RuntimeException;

/**
 * Carga las variables de configuración desde el archivo .env del proyecto.
 *
 * @return void
 *
 * @throws RuntimeException Si el archivo no existe o contiene una línea inválida.
 */
function loadEnvironment(): void
{
    $environmentPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';

    if (!is_readable($environmentPath)) {
        throw new RuntimeException('La configuración de la aplicación no está disponible.');
    }

    $lines = file(
        $environmentPath,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    if ($lines === false) {
        throw new RuntimeException('No se pudo leer la configuración de la aplicación.');
    }

    foreach ($lines as $line) {
        $trimmedLine = trim($line);

        if ($trimmedLine === '' || str_starts_with($trimmedLine, '#')) {
            continue;
        }

        $separatorPosition = strpos($trimmedLine, '=');

        if ($separatorPosition === false) {
            throw new RuntimeException('La configuración de la aplicación es inválida.');
        }

        $key = trim(substr($trimmedLine, 0, $separatorPosition));
        $value = trim(substr($trimmedLine, $separatorPosition + 1));

        if ($key === '') {
            throw new RuntimeException('La configuración de la aplicación es inválida.');
        }

        if (
            strlen($value) >= 2
            && (($value[0] === '"' && $value[-1] === '"')
                || ($value[0] === "'" && $value[-1] === "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        putenv($key . '=' . $value);
    }
}

/**
 * Crea la conexión PDO con la base de datos de la aplicación.
 *
 * @return PDO Conexión configurada para lanzar excepciones y devolver filas asociativas.
 *
 * @throws RuntimeException Si falta una variable de entorno o falla la conexión.
 */
function createPdoConnection(): PDO
{
    loadEnvironment();

    $host = getenv('DB_HOST');
    $database = getenv('DB_NAME');
    $username = getenv('DB_USER');
    $password = getenv('DB_PASS');

    if (
        $host === false
        || $database === false
        || $username === false
        || $password === false
        || $host === ''
        || $database === ''
        || $username === ''
    ) {
        throw new RuntimeException('La configuración de la base de datos está incompleta.');
    }

    $dsn = 'mysql:host=' . $host
        . ';dbname=' . $database
        . ';charset=utf8mb4';

    try {
        return new PDO(
            $dsn,
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (\Throwable $exception) {
        throw new RuntimeException(
            'No se pudo establecer la conexión con la base de datos.',
            0,
            $exception
        );
    }
}
