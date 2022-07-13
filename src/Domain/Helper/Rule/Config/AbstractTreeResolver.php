<?php

namespace Obblm\Core\Domain\Helper\Rule\Config;

abstract class AbstractTreeResolver
{
    public static function getChildren(): array
    {
        return [];
    }
}
