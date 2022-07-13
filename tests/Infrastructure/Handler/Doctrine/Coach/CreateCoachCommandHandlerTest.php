<?php

namespace Obblm\Core\Tests\Infrastructure\Handler\Doctrine\Coach;

use Obblm\Core\Domain\Service\Coach\CoachService;
use Obblm\Core\Infrastructure\Handler\Doctrine\Coach\CreateCoachCommandHandler;
use Obblm\Core\Infrastructure\Repository\Doctrine\CoachRepository;
use Obblm\Core\Tests\Fixtures\Command\CreateCoachCommandBuilder;
use Obblm\Core\Tests\MessageTestCase;

class CreateCoachCommandHandlerTest extends MessageTestCase
{
    protected CoachService $service;
    protected ?CoachRepository $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = static::getContainer()->get(CoachRepository::class);
        $handler = static::getContainer()->get(CreateCoachCommandHandler::class);
        $this->service = new CoachService($this->messageBus, $this->repository);
        $this->messageBus->addHandler($handler);
    }

    /**
     * @test I want to create a new coach
     * @when invoke create method in CoachService
     * @then the caoch is inserted in database
     */
    public function testCoachCreateService()
    {
        $command = CreateCoachCommandBuilder::for()->build();
        $this->service->create($command);

        $coach = $this->repository->loadUserByUsername($command->getUsername());

        self::assertSame($command->getUsername(), $coach->getUsername());
        self::assertSame($command->getEmail(), $coach->getEmail());

        $this->repository->delete($coach);
    }

    /**
     * @test I want to create a new coach
     * @when I create a caoch
     * @then the caoch username exists
     */
    public function testCoachUsernameExists()
    {
        $command = CreateCoachCommandBuilder::for()->build();
        $this->service->create($command);

        self::assertEquals(true, $this->service->isUsernameExists($command->getUsername()));

        $coach = $this->repository->loadUserByUsername($command->getUsername());
        $this->repository->delete($coach);
    }
}
