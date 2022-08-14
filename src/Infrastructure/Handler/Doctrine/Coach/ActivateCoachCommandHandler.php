<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Handler\Doctrine\Coach;

use Obblm\Core\Domain\Command\Coach\ActivateCoachCommand;
use Obblm\Core\Domain\Event\CoachEvent;
use Obblm\Core\Domain\Handler\Coach\ActivateCoachCommandInterface;
use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Infrastructure\Repository\Doctrine\CoachRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ActivateCoachCommandHandler implements ActivateCoachCommandInterface
{
    private CoachRepository $coachRepository;
    private EventDispatcherInterface $dispatcher;

    public function __construct(CoachRepository $coachRepository, EventDispatcherInterface $dispatcher)
    {
        $this->coachRepository = $coachRepository;
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(ActivateCoachCommand $command): Coach
    {
        $coach = $this->coachRepository->findOneByHash($command->getHash());

        $coach->setActive(true)
            ->setHash(null);
        $this->coachRepository->save($coach);

        $this->dispatcher->dispatch($coach, CoachEvent::ACTIVATED);

        return $coach;
    }
}
