<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Service;

use Obblm\Core\Domain\Command\CommandInterface;
use Obblm\Core\Domain\Contracts\ObblmBusInterface;
use Obblm\Core\Domain\Notification\NotificationInterface;

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

    public function save(CommandInterface $command)
    {
        $this->messageBus->dispatch($command);
    }

    public function delete(CommandInterface $command)
    {
        $this->messageBus->dispatch($command);
    }

    public function notify(NotificationInterface $notification)
    {
        $this->messageBus->dispatch($notification);
    }
}
