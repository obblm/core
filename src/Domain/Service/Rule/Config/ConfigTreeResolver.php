<?php

namespace Obblm\Core\Domain\Service\Rule\Config;

use Obblm\Core\Domain\Contracts\ConfigTreeInterface;

class ConfigTreeResolver extends ConfigResolver
{
    public function __construct(ConfigTreeInterface $configuration)
    {
        parent::__construct($configuration);
    }
}
