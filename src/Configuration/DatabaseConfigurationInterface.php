<?php

namespace Thor\Database\PdoExtension\Configuration;

/**
 * Minimal interface for database connection configuration.
 *
// * Implementations should provide DSN, optional user/password, and arbitrary driver options.
 */
interface DatabaseConfigurationInterface
{

    public function getDsn(): string;
    public function getUser(): ?string;
    public function getPassword(): ?string;
    public function getOptions(): array;

}
