<?php

namespace Thor\Database\PdoExtension;

use PDOStatement;
use PDOException;

/**
 * Performs SQL queries on a PDO connection managed by a Handler.
 *
 * Provides simple helpers to execute statements and fetch results with positional parameters.
 *
 * Example
 * ```
 * $handler = new Handler('sqlite::memory:');
 * $req     = new Requester($handler);
 * $req->execute('CREATE TABLE t (id INTEGER PRIMARY KEY, name TEXT)');
 * $req->execute('INSERT INTO t (name) VALUES (?)', ['Alice']);
 * $row = $req->request('SELECT * FROM t WHERE name = ?', ['Alice'])->fetch();
 * ```
 */
class Requester
{

    /**
     * @param Handler $handler
     */
    public function __construct(protected Handler $handler)
    {
    }

    /**
     * Executes a parameterized SQL-query with the PdoHandler.
     *
     * @param string $sql
     * @param array  $parameters
     *
     * @return bool
     */
    public function execute(string $sql, array $parameters = []): bool
    {
        $stmt = $this->handler->getPdo()->prepare($sql);

        return $stmt->execute(array_values($parameters));
    }

    /**
     * Executes a parameterized SQL-query with the PdoHandler multiple times (one time for each array in $parameters).
     *
     * @param string  $sql
     * @param array[] $parameters
     * @param bool    $continueIfError
     *
     * @return bool
     */
    public function executeMultiple(string $sql, array $parameters, bool $continueIfError = false): bool
    {
        $stmt = $this->handler->getPdo()->prepare($sql);
        $result = true;

        foreach ($parameters as $pdoRowsArray) {
            try {
                $result = $result && $stmt->execute(array_values($pdoRowsArray));
            } catch (PDOException $e) {
                if (!$continueIfError) {
                    throw $e;
                }
            }
        }

        return $result;
    }

    /**
     * Executes a parameterized SQL-query with the PdoHandler and returns the result as a PDOStatement object.
     *
     * @param string $sql
     * @param array $parameters
     *
     * @return PDOStatement
     */
    public function request(string $sql, array $parameters = []): PDOStatement
    {
        $stmt = $this->handler->getPdo()->prepare($sql);
        $stmt->execute(array_values($parameters));

        return $stmt;
    }

    /**
     * Returns this instance's PDO connection handler.
     */
    final public function getPdoHandler(): Handler
    {
        return $this->handler;
    }

    /**
     * Format a string like "(?, ?, ?, ...)" where number of '?' is `count($elements)`
     *
     * @param array $elements
     *
     * @return string
     */
    public static function in(array $elements) : string
    {
        return '(' . implode(',', array_fill(0, count($elements), '?')) . ')';
    }

}
