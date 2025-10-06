<?php

namespace Thor\Database\PdoExtension;

use PDO;
use PDOException;

/**
 * Lightweight PDO wrapper that defers the actual DB connection until first use.
 *
 * Call getPdo() to obtain a configured PDO instance. Connection is established on demand.
 *
 * Example
 * ```
 * $handler = new Handler('sqlite::memory:');
 * $pdo = $handler->getPdo(); // connects now
 * $pdo->exec('CREATE TABLE t (id INTEGER PRIMARY KEY, name TEXT)');
 * ```
 *
 * @see PDO
 */
final class Handler
{

    private ?PDO $pdo = null;

    /**
     * Constructs a new PdoHandler.
     */
    public function __construct(
        public readonly string $dsn,
        public readonly ?string $user = null,
        public readonly ?string $password = null,
        public readonly int $defaultCase = PDO::CASE_NATURAL,
        public readonly array $customOptions = [],
    ) {
    }

    /**
     * @return string|null
     */
    public function getDriverName(): ?string
    {
        return explode(':', $this->dsn)[0] ?: null;
    }

    /**
     * Returns true if PDO object has been constructed, false otherwise.
     */
    public function isConnected(): bool
    {
        return $this->pdo !== null;
    }

    /**
     * Returns the current PDO object or constructs it with the PdoHandler parameters.
     *
     * @throws PDOException
     */
    public function getPdo(): PDO
    {
        return $this->pdo ??= new PDO(
            $this->dsn,
            $this->user,
            $this->password,
            $this->customOptions +
            [
                PDO::ATTR_CASE               => $this->defaultCase,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            ]
        );
    }

}
