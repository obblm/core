<?php

namespace Obblm\Core\Resources\Rule\Config;

class ConfigTreeResolver extends ConfigResolver
{
    public function __construct(ConfigTreeInterface $configuration)
    {
        parent::__construct($configuration);
    }
}
