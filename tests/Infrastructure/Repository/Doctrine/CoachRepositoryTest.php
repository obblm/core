<?php

declare(strict_types=1);

namespace Obblm\Core\Tests\Infrastructure\Repository\Doctrine;

use Obblm\Core\Domain\Command\Coach\CreateCoachCommand;
use Obblm\Core\Domain\Service\Coach\CoachService;
use Obblm\Core\Infrastructure\Handler\Doctrine\Coach\CreateCoachCommandHandler;
use Obblm\Core\Infrastructure\Repository\Doctrine\CoachRepository;
use Obblm\Core\Tests\Fixtures\Command\CreateCoachCommandBuilder;
use Obblm\Core\Tests\Fixtures\Doctrine\CoachBuilder;
use Obblm\Core\Tests\MessageTestCase;

class CoachRepositoryTest extends MessageTestCase
{
    protected CoachService $service;
    protected CreateCoachCommand $command;
    protected ?CoachRepository $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = static::getContainer()->get(CoachRepository::class);
        $handler = static::getContainer()->get(CreateCoachCommandHandler::class);
        $this->service = new CoachService($this->messageBus, $this->repository);
        $this->messageBus->addHandler($handler);

        $this->command = CreateCoachCommandBuilder::for()->build();
        $this->service->create($this->command);
    }

    /**
     * @test I want to get a coach
     * @when I get the coach by username
     * @then the caoch is loaded
     */
    public function testLoadCoachByUsername()
    {
        $coach = $this->repository->loadUserByUsername($this->command->getUsername());

        self::assertSame($this->command->getUsername(), $coach->getUsername());
        self::assertSame($this->command->getEmail(), $coach->getEmail());
    }

    /**
     * @test I want to get a coach
     * @when I get the coach by email
     * @then the caoch is loaded
     */
    public function testLoadCoachByEmail()
    {
        $coach = $this->repository->loadUserByEmail($this->command->getEmail());

        self::assertSame($this->command->getUsername(), $coach->getUsername());
        self::assertSame($this->command->getEmail(), $coach->getEmail());
    }

    /**
     * @test I want to get a coach
     * @when I get the coach by email or username
     * @then the caoch is loaded
     */
    public function testLoadCoachByUsernameOrEmail()
    {
        $coachByEmail = $this->repository->loadUserByUsernameOrEmail($this->command->getEmail());
        $coachByUsername = $this->repository->loadUserByUsernameOrEmail($this->command->getUsername());

        self::assertSame($coachByEmail, $coachByUsername);
    }

    /**
     * @test I want to reset a coach password
     * @when I get the coach by hash without a good limit date
     * @then null is returned
     */
    public function testLoadUserForPasswordReset()
    {
        $coach = $this->repository->loadUserByEmail($this->command->getEmail());
        $forResetCoach = $this->repository->findOneForPasswordReset($coach->getHash());

        self::assertEquals(null, $forResetCoach);
    }

    /**
     * @test I want to create a new coach
     * @when invoke create method in CoachService
     * @then a CreateCoachCommandBuilder message is dispatched
     */
    public function testGetCoachService()
    {
        $coach = CoachBuilder::for(static::getContainer())->build();

        $loaded = $this->repository->get($coach->getId());

        self::assertSame($coach, $loaded);
    }

    public function tearDown(): void
    {
        $coach = $this->repository->loadUserByUsername($this->command->getUsername());
        $this->repository->delete($coach);
        parent::tearDown(); // TODO: Change the autogenerated stub
    }
}