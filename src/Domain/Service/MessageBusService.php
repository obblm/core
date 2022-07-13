<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Service;

use Obblm\Core\Domain\Command\CommandInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class MessageBusService
{
    protected MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function create(CommandInterface $command)
    {
        $this->messageBus->dispatch($command);
    }
}
