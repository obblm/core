<?php

declare(strict_types=1);

namespace Obblm\Core\Tests;

use Obblm\Core\Domain\Contracts\ObblmBusInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class MessageTestCase extends KernelTestCase
{
    protected ObblmBusInterface $messageBus;

    public function setUp(): void
    {
        static::bootKernel();
        $this->messageBus = $this->mockMessageBus();
    }

    private function mockMessageBus(): ObblmBusInterface
    {
        return new class() implements ObblmBusInterface {
            private object $dispatchedCommand;
            private array $handlers = [];

            public function addHandler(?object $handler)
            {
                $this->handlers[] = $handler;
            }

            public function dispatch($message, array $stamps = []): Envelope
            {
                $this->dispatchedCommand = $message;
                $envelope = new Envelope($message);

                foreach ($this->handlers as $handler) {
                    $handler($envelope->getMessage());
                }

                return $envelope;
            }

            public function lastDispatchedCommand(): object
            {
                return $this->dispatchedCommand;
            }
        };
    }
}
