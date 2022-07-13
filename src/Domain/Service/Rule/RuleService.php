<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Service\Rule;

use Obblm\Core\Domain\Model\Rule;
use Obblm\Core\Domain\Repository\RuleRepositoryInterface;
use Obblm\Core\Domain\Service\MessageBusService;
use Symfony\Component\Messenger\MessageBusInterface;

class RuleService extends MessageBusService
{
    private RuleRepositoryInterface $repository;

    public function __construct(MessageBusInterface $messageBus, RuleRepositoryInterface $repository)
    {
        parent::__construct($messageBus);
        $this->repository = $repository;
    }

    public function get($id): ?Rule
    {
        return $this->repository->get($id);
    }
}
