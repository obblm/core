<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Command\Email;

class CreateEmailCommand
{
    private string $to;

    public function __construct(
        string $to
    ) {
        $this->to = $to;
    }
}
