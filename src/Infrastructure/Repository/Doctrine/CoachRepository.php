<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Repository\Doctrine;

use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Repository\CoachRepositoryInterface;
use Obblm\Core\Infrastructure\Shared\Persistence\Doctrine\DoctrineRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

class CoachRepository extends DoctrineRepository implements CoachRepositoryInterface
{
    public function save(Coach $coach): Coach
    {
        $this->persist($coach);

        return $coach;
    }

    public function delete(Coach $coach): void
    {
        $this->remove($coach);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof Coach) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->persist($user);
    }

    public function get($id): ?Coach
    {
        return $this->repository(Coach::class)->find($id);
    }

    public function loadUserByUsernameOrEmail($usernameOrEmail): ?Coach
    {
        return $this->repository(Coach::class)->createQueryBuilder('c')
            ->where('c.username = :username_or_email')
            ->orWhere('c.email = :username_or_email')
            ->getQuery()
            ->setParameter(':username_or_email', $usernameOrEmail)
            ->getOneOrNullResult();
    }

    public function loadUserByUsername($username): ?Coach
    {
        return $this->repository(Coach::class)->createQueryBuilder('c')
            ->where('c.username = :username')
            ->getQuery()
            ->setParameter(':username', $username)
            ->getOneOrNullResult();
    }

    public function loadUserByEmail($email): ?Coach
    {
        return $this->repository(Coach::class)->createQueryBuilder('c')
            ->where('c.email = :email')
            ->getQuery()
            ->setParameter(':email', $email)
            ->getOneOrNullResult();
    }

    public function findOneForPasswordReset($hash): ?Coach
    {
        $limitDateTime = (new \DateTime('- 1 day'))->format('Y-m-d H:m:i');

        return $this->repository(Coach::class)->createQueryBuilder('c')
            ->where('c.resetPasswordHash = :hash')
            ->andWhere('c.resetPasswordAt > :limit_datetime')
            ->getQuery()
            ->setParameter(':hash', $hash)
            ->setParameter(':limit_datetime', $limitDateTime)
            ->getOneOrNullResult();
    }
}
