<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Service;

use Obblm\Core\Domain\Command\CommandInterface;
use Obblm\Core\Domain\Contracts\ObblmBusInterface;

class MessageBusService
{
    protected ObblmBusInterface $messageBus;

    public function __construct(ObblmBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function create(CommandInterface $command)
    {
        $this->messageBus->dispatch($command);
    }
}
