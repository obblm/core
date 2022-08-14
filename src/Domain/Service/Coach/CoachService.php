<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Service\Coach;

use Obblm\Core\Domain\Contracts\ObblmBusInterface;
use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Repository\CoachRepositoryInterface;
use Obblm\Core\Domain\Service\MessageBusService;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CoachService extends MessageBusService implements UserProviderInterface
{
    protected CoachRepositoryInterface $repository;

    public function __construct(ObblmBusInterface $messageBus, CoachRepositoryInterface $repository)
    {
        parent::__construct($messageBus);
        $this->repository = $repository;
    }

    public function get(string $id): ?Coach
    {
        return $this->repository->get($id);
    }

    public function loadUserByLogin(string $username): ?Coach
    {
        return $this->repository->loadUserByUsernameOrEmail($username);
    }

    public function isUsernameExists(string $username): bool
    {
        return $this->repository->loadUserByUsername($username) ? true : false;
    }

    public function isEmailExists(string $username): bool
    {
        return $this->repository->loadUserByEmail($username) ? true : false;
    }

    public function findOneByHash(string $hash): ?Coach
    {
        return $this->repository->findOneByHash($hash);
    }

    /**
     * UserProviderInterface methods.
     */
    public function loadUserByUsername(string $username): ?Coach
    {
        return $this->loadUserByLogin($username);
    }

    public function refreshUser(UserInterface $user): ?Coach
    {
        return $this->loadUserByLogin($user->getUsername());
    }

    public function supportsClass(string $class): bool
    {
        return Coach::class === $class;
    }
}
