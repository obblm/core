<?php

namespace Obblm\Core\Domain\Service\Rule\Config;

abstract class AbstractTreeResolver
{
    public static function getChildren(): array
    {
        return [];
    }
}
