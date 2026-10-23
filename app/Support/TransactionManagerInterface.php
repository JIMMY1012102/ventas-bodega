<?php
declare(strict_types=1);
namespace App\Support;
/** Abstrae el control de transacciones para producción y pruebas. */
interface TransactionManagerInterface
{
    /** Inicia una transacción. */
    public function beginTransaction(): void;
    /** Confirma una transacción. */
    public function commit(): void;
    /** Revierte una transacción activa. */
    public function rollBack(): void;
}

