<?php

namespace Obblm\Core\Tests\Domain\Service;

use Doctrine\ORM\EntityManagerInterface;
use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Service\Coach\CoachService;
use Obblm\Core\Infrastructure\Repository\Doctrine\CoachRepository;
use Obblm\Core\Tests\Fixtures\Command\CreateCoachCommandBuilder;
use Obblm\Core\Tests\Fixtures\Doctrine\CoachBuilder;
use Obblm\Core\Tests\MessageTestCase;

class CoachServiceTest extends MessageTestCase
{
    protected CoachService $service;
    protected ?CoachRepository $repository;
    protected EntityManagerInterface $entityManager;
    protected Coach $expectedCoach;

    public function setUp(): void
    {
        parent::setUp();
        $this->entityManager = static::getContainer()
            ->get('doctrine')
            ->getManager();

        $this->expectedCoach = CoachBuilder::for(static::getContainer())->build();

        $this->repository = $this->mockRepository();
        $this->service = new CoachService($this->messageBus, $this->repository);
    }

    /**
     * @test I want to create a new coach
     * @when invoke create method in CoachService
     * @then a CreateCoachCommandBuilder message is dispatched
     */
    public function testCoachCreateService()
    {
        $command = CreateCoachCommandBuilder::for()->build();
        $this->service->create($command);

        self::assertSame($command, $this->messageBus->lastDispatchedCommand());
        self::assertSame($command->getUsername(), $this->messageBus->lastDispatchedCommand()->getUsername());
        self::assertSame($command->getEmail(), $this->messageBus->lastDispatchedCommand()->getEmail());
        self::assertSame($command->getPlainPassword(), $this->messageBus->lastDispatchedCommand()->getPlainPassword());
    }

    public function testGetService()
    {
        $coach = $this->service->get($this->expectedCoach->getId());

        self::assertSame($this->expectedCoach, $coach);
    }

    public function testLoadByLoginService()
    {
        $coach = $this->service->loadUserByLogin($this->expectedCoach->getUsername());

        self::assertSame($this->expectedCoach, $coach);
    }

    public function testIsEmailExistsService()
    {
        $exists = $this->service->isEmailExists('toto@toto.com');

        self::assertSame(false, $exists);
    }

    public function testRefreshUser()
    {
        $coach = $this->service->refreshUser($this->expectedCoach);

        self::assertSame($this->expectedCoach, $coach);
    }

    public function testProviderSupportOK()
    {
        self::assertEquals(true, $this->service->supportsClass(get_class($this->expectedCoach)));
    }

    public function testProviderSupportKO()
    {
        self::assertEquals(false, $this->service->supportsClass('Obblm\Core\Application\Model\User'));
    }

    private function mockRepository()
    {
        $repository = $this->createMock(CoachRepository::class);

        $repository->expects($this->any())
            ->method('get')
            ->willReturn($this->expectedCoach);

        $repository->expects($this->any())
            ->method('loadUserByUsernameOrEmail')
            ->willReturn($this->expectedCoach);

        $repository->expects($this->any())
            ->method('loadUserByEmail')
            ->willReturn(null);

        return $repository;
    }
}
