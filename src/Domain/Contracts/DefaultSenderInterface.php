<?php

namespace Obblm\Core\Domain\Contracts;

interface DefaultSenderInterface
{
    public function setDefaultSender(string $defaultSenderAddress, string $defaultSenderName);
}
