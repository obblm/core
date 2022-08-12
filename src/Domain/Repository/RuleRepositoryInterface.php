<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Repository;

use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Model\Rule;

interface RuleRepositoryInterface
{
    public function save(Rule $team): void;

    public function get($id): ?Rule;

    public function getByKey($key): ?Rule;

    public function findAllowedRules(Coach $admin);

    public function findAll();
}
