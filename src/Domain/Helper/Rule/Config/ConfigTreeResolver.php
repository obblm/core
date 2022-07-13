<?php

namespace Obblm\Core\Domain\Helper\Rule\Config;

class ConfigTreeResolver extends ConfigResolver
{
    public function __construct(ConfigTreeInterface $configuration)
    {
        parent::__construct($configuration);
    }
}
