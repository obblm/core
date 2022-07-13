<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Repository\Doctrine;

use Obblm\Core\Domain\Model\Rule;
use Obblm\Core\Domain\Repository\RuleRepositoryInterface;
use Obblm\Core\Infrastructure\Shared\Persistence\Doctrine\DoctrineRepository;

class RuleRepository extends DoctrineRepository implements RuleRepositoryInterface
{
    public function save(Rule $rule): void
    {
        $this->persist($rule);
    }

    public function delete(Rule $rule): void
    {
        $this->remove($rule);
    }

    public function get($id): ?Rule
    {
        return $this->repository(Rule::class)->find($id);
    }

    public function getByKey($key): ?Rule
    {
        return $this->repository(Rule::class)->findOneBy(['key' => $key]);
    }
}
