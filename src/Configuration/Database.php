<?php

namespace Thor\Database\PdoExtension\Configuration;

use Thor\Common\Configuration\Configuration;
use Thor\Common\Configuration\ConfigurationFromFile;

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

    public static function loadMultiple(string $path): array
    {
        return ConfigurationFromFile::multipleFromFile($path, self::class);
    }

    public function getDsn(): string
    {
        return $this['dsn'];
    }

    public function getUser(): ?string
    {
        return $this['user'] ?? null;
    }

    public function getPassword(): ?string
    {
        return $this['password'] ?? null;
    }

    public function getOptions(): array
    {
        return $this['options'] ?? [];
    }

}
