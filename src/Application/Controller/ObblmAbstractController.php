<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller;

use Obblm\Core\Domain\Command\AbstractCommand;
use Obblm\Core\Domain\Command\CommandInterface;
use Obblm\Core\Domain\Exception\Command\MissingCommandKeyException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

abstract class ObblmAbstractController extends AbstractController
{
    public function commandFromRequest(string $class, Request $request): ?CommandInterface
    {
        $data = [];
        foreach ($class::CONSTRUCTOR_ARGUMENTS as $key) {
            if (!$request->get($key)) {
                throw new MissingCommandKeyException($class, $key);
            }
            $data[$key] = $request->get($key);
        }

        return AbstractCommand::fromArray($class, $data);
    }

    public function commandFromArray(string $class, array $data): ?CommandInterface
    {
        return AbstractCommand::fromArray($class, $data);
    }
}
