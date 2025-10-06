<?php

namespace Thor\Database\PdoExtension;

/**
 * Requester wrapper that begins a transaction and commits/rolls back automatically.
 *
 * If $autoTransaction is true (default), a transaction is started on construction (if none is active)
 * and committed on destruction; if false, no auto-commit occurs and an active transaction will be rolled back
 * on destruction unless you call commit() explicitly.
 *
 * Example
 * ```
 * $t = new Transaction(new Handler('sqlite::memory:'));
 * $t->execute('CREATE TABLE t (id INTEGER PRIMARY KEY, name TEXT)');
 * $t->execute('INSERT INTO t (name) VALUES (?)', ['Alice']);
 * // committed automatically when $t is destroyed
 * ```
 */
final class Transaction extends Requester
{

    /**
     * @param Handler $handler
     * @param bool       $autoTransaction
     */
    public function __construct(Handler $handler, private bool $autoTransaction = true)
    {
        parent::__construct($handler);
        if ($this->autoTransaction && !$this->handler->getPdo()->inTransaction()) {
            $this->begin();
        }
    }

    /**
     *
     */
    public function __destruct()
    {
        if ($this->handler->getPdo()->inTransaction()) {
            if ($this->autoTransaction) {
                $this->commit();
            } else {
                $this->rollback();
            }
        }
    }

    /**
     * Sends a beginTransaction() to PDO.
     */
    public function begin(): void
    {
        $this->handler->getPdo()->beginTransaction();
    }

    /**
     * Sends a commit() to PDO.
     */
    public function commit(): void
    {
        $this->handler->getPdo()->commit();
    }

    /**
     * Sends a rollBack() to PDO.
     */
    public function rollback(): void
    {
        $this->handler->getPdo()->rollBack();
    }

}
