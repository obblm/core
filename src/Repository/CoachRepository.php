<?php

namespace Obblm\Core\Repository;

use Obblm\Core\Entity\Coach;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Coach|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coach|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coach[]    findAll()
 * @method Coach[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoachRepository extends ServiceEntityRepository implements UserLoaderInterface, PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coach::class);
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
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function loadUserByUsername($usernameOrEmail)
    {
        return $this->createQueryBuilder('c')
            ->where('c.username = :username_or_email')
            ->orWhere('c.email = :username_or_email')
            ->getQuery()
            ->setParameter(':username_or_email', $usernameOrEmail)
            ->getOneOrNullResult();
    }

    public function findOneForPasswordReset($hash)
    {
        $limitDateTime = (new \DateTime('- 1 day'))->format("Y-m-d H:m:i");
        return $this->createQueryBuilder('c')
            ->where('c.resetPasswordHash = :hash')
            ->andWhere('c.resetPasswordAt > :limit_datetime')
            ->getQuery()
            ->setParameter(':hash', $hash)
            ->setParameter(':limit_datetime', $limitDateTime)
            ->getOneOrNullResult();
    }
}
