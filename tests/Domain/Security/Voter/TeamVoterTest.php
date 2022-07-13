<?php

declare(strict_types=1);

namespace Obblm\Core\Tests\Domain\Security\Voter;

use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Model\Team;
use Obblm\Core\Domain\Security\Voter\TeamVoter;
use Obblm\Core\Infrastructure\Model\Doctrine\Player;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;

class TeamVoterTest extends TestCase
{
    private AccessDecisionManager $checker;
    private TeamVoter $voter;

    public function setUp(): void
    {
        $this->voter = new TeamVoter();
        $this->checker = new AccessDecisionManager([$this->voter]);
    }

    /**
     * @test
     */
    public function testVoterSupportsTeam()
    {
        $coach = new Coach();
        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->any())
            ->method('getUser')
            ->willReturn($coach);
        $team = (new Team())
            ->setName('Test')
            ->setCoach($coach);
        self::assertTrue($this->checker->decide($token, [TeamVoter::VIEW], $team));
    }

    public function testVoterDoesNotSupportOther()
    {
        $player = (new Player())
            ->setName('Test');
        $token = $this->createMock(TokenInterface::class);
        self::assertFalse($this->checker->decide($token, [TeamVoter::VIEW], $player));
    }

    public function testVoterDoesNotSupportNotCoach()
    {
        $team = (new Team())
            ->setName('Test');
        $token = $this->createMock(TokenInterface::class);
        self::assertFalse($this->checker->decide($token, [TeamVoter::VIEW], $team));
    }

    public function testVoterDoesNotSupportValideAttribute()
    {
        $coach = new Coach();
        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->any())
            ->method('getUser')
            ->willReturn($coach);
        $team = (new Team())
            ->setName('Test')
            ->setCoach($coach);
        self::assertTrue($this->checker->decide($token, [TeamVoter::VIEW], $team));
        self::assertTrue($this->checker->decide($token, [TeamVoter::EDIT], $team));
        self::assertFalse($this->checker->decide($token, ['test'], $team));
    }

    public function tearDown(): void
    {
    }
}
