<?php

namespace Thor\Database\PdoExtension\Configuration;

interface DatabaseConfigurationInterface
{

    public function getDsn(): string;
    public function getUser(): ?string;
    public function getPassword(): ?string;
    public function getOptions(): array;

}
