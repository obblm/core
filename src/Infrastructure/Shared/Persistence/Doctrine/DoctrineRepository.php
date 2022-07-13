<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Shared\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;

abstract class DoctrineRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    protected function persist($entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    protected function remove($entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    protected function repository(string $entityClass): ObjectRepository
    {
        return $this->em->getRepository($entityClass);
    }
}
