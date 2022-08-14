<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Notification;

use Obblm\Core\Domain\Command\CommandInterface;

interface NotificationInterface extends CommandInterface
{
    /**
     * @return mixed
     */
    public function getContent();

    public function getContext(): array;
}
