<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Security;

abstract class Roles
{
    const ADMIN = 'ROLE_OBBLM_ADMIN';
    const MANAGER = 'ROLE_OBBLM_MANAGER';
    const COACH = 'ROLE_OBBLM_COACH';
}
