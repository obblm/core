<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Handler\Doctrine\Coach;

use Obblm\Core\Domain\Command\Coach\CreateCoachCommand;
use Obblm\Core\Domain\Handler\Coach\CreateCoachCommandHandlerInterface;
use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Service\Hash;
use Obblm\Core\Infrastructure\Repository\Doctrine\CoachRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateCoachCommandHandler implements CreateCoachCommandHandlerInterface
{
    private CoachRepository $coachRepository;
    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(CoachRepository $coachRepository, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->coachRepository = $coachRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function __invoke(CreateCoachCommand $command): Coach
    {
        $coach = (new Coach())
            ->setEmail($command->getEmail())
            ->setUsername($command->getUsername())
            ;
        $password = $this->passwordEncoder->hashPassword($coach, $command->getPlainPassword());

        $coach->setPassword($password)
            ->setHash((new Hash())($coach->getEmail()));
        $this->coachRepository->save($coach);

        return $coach;
    }
}
