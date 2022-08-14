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

    public function loadUserByUsernameOrEmail(string $usernameOrEmail): ?Coach;

    public function loadUserByUsername(string $username): ?Coach;

    public function loadUserByEmail(string $email): ?Coach;

    public function findOneForPasswordReset(string $hash): ?Coach;

    public function findOneByHash(string $hash): ?Coach;
}
