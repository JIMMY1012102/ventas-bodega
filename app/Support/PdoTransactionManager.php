<?php
declare(strict_types=1);
namespace App\Support;
use PDO;

/** Gestiona transacciones delegando en PDO. */
final class PdoTransactionManager implements TransactionManagerInterface
{
    /** @param PDO $pdo Conexión PDO activa. */
    public function __construct(private PDO $pdo) {}
    /** Inicia una transacción PDO. */
    public function beginTransaction(): void { $this->pdo->beginTransaction(); }
    /** Confirma una transacción PDO. */
    public function commit(): void { $this->pdo->commit(); }
    /** Revierte solo si hay una transacción activa. */
    public function rollBack(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }
}

