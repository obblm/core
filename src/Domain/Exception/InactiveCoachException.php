<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class InactiveCoachException extends CustomUserMessageAuthenticationException
{
    public const MESSAGE = 'obblm.login.not_active';
}
