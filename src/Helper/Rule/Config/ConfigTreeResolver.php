<?php

namespace Obblm\Core\Helper\Rule\Config;

class ConfigTreeResolver extends ConfigResolver
{
    public function __construct(ConfigTreeInterface $configuration)
    {
        parent::__construct($configuration);
    }
}
