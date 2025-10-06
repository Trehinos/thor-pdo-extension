<?php

namespace Thor\Database\PdoExtension\Configuration;

use Thor\Common\Configuration\Configuration;
use Thor\Common\Configuration\ConfigurationFromFile;

/**
 * Database connection configuration implementation.
 *
 * Keys: dsn (string), user (?string), password (?string), options (array). A legacy top-level 'case'
 * value is normalized into options['case'] by the constructor for backward compatibility.
 *
 * Example
 * ```
 *   $db = new Database([
 *       'dsn' => 'sqlite::memory:',
 *       'options' => ['case' => 'natural']
 *   ]);
 *   $dsn = $db->getDsn();
 * ```
 */
class Database extends Configuration implements DatabaseConfigurationInterface
{

    public function __construct(array $configArray = [])
    {
        if (array_key_exists('case', $configArray)) {
            $configArray['options']['case'] = $configArray['case'];
            $configArray['case']            = null;
            unset($configArray['case']);
        }
        parent::__construct($configArray);
    }

    /**
     * Load multiple Database configurations from a file path supported by Thor Common Configuration.
     *
     * @param string $path path to configuration file
     * @return array<string, Database> associative array of connectionName => Database config
     */
    public static function loadMultiple(string $path): array
    {
        return ConfigurationFromFile::multipleFromFile($path, self::class);
    }

    /**
     * Get the PDO DSN string.
     */
    public function getDsn(): string
    {
        return $this['dsn'];
    }

    /**
     * Get the username for the connection if any.
     */
    public function getUser(): ?string
    {
        return $this['user'] ?? null;
    }

    /**
     * Get the password for the connection if any.
     */
    public function getPassword(): ?string
    {
        return $this['password'] ?? null;
    }

    /**
     * Get driver options. Recognized option: 'case' ('upper'|'lower'|'natural').
     */
    public function getOptions(): array
    {
        return $this['options'] ?? [];
    }

}
