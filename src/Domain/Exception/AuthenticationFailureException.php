<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class AuthenticationFailureException extends CustomUserMessageAuthenticationException
{
    public const MESSAGE = 'obblm.login.invalid_credentials';
}
