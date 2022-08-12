<?php

namespace Obblm\Core\Infrastructure\Handler;

use Obblm\Core\Domain\Command\CommandInterface;
use Obblm\Core\Domain\Contracts\ObblmBusInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SymfonyMessageBus implements ObblmBusInterface
{
    protected MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function dispatch(CommandInterface $command)
    {
        $this->messageBus->dispatch($command);
    }
}
