<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Repository;

use Obblm\Core\Domain\Model\Coach;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

interface CoachRepositoryInterface extends UserLoaderInterface, PasswordUpgraderInterface
{
    public function get($id): ?Coach;

    public function save(Coach $coach): Coach;

    public function delete(Coach $coach): void;

    public function loadUserByUsernameOrEmail($usernameOrEmail): ?Coach;

    public function loadUserByUsername($username): ?Coach;

    public function loadUserByEmail($email): ?Coach;

    public function findOneForPasswordReset($hash): ?Coach;
}
