<?php

namespace Obblm\Core\Domain\Command;

use Obblm\Core\Domain\Exception\Command\CommandKeysException;
use Obblm\Core\Domain\Exception\Command\CreateCommandException;
use Obblm\Core\Domain\Exception\Command\MissingCommandKeyException;

abstract class AbstractCommand
{
    const CONSTRUCTOR_ARGUMENTS = null;

    /**
     * @return ?CommandInterface
     */
    public static function fromArray(string $class, array $data): ?CommandInterface
    {
        if (null == $class::CONSTRUCTOR_ARGUMENTS) {
            throw new CommandKeysException($class);
        }
        $args = [];
        foreach ($class::CONSTRUCTOR_ARGUMENTS as $key) {
            if (!isset($data[$key])) {
                throw new MissingCommandKeyException($class, $key);
            }
            $args[$key] = $data[$key];
        }
        try {
            $reflection = new \ReflectionClass($class);
            $obj = $reflection->newInstanceArgs($args);
        } catch (\Exception $e) {
            throw new CreateCommandException($class);
        }

        if (!$obj instanceof CommandInterface && !$obj instanceof $class) {
            throw new CreateCommandException($class);
        }

        return $obj;
    }
}
