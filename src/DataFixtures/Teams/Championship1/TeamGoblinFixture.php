<?php

namespace Obblm\Core\DataFixtures\Teams\Championship1;

use Obblm\Core\DataFixtures\ChampionshipFixtures;
use Obblm\Core\DataFixtures\Teams\CoachFixtures;
use Obblm\Core\DataFixtures\Teams\TeamFixtureTrait;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TeamGoblinFixture extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    use TeamFixtureTrait;

    const COACH_NUMBER = 8;

    public function load(ObjectManager $em)
    {
        $this->setChampionship($this->getReference(ChampionshipFixtures::CHAMPIONSHIP_REFERENCE))
            ->setCoach($this->getReference( CoachFixtures::COACH_USER_REFERENCE . '-' . self::COACH_NUMBER));

        $data = [
            'name' => 'Team Goblin',
            'roster' => 'goblin',
            'rerolls' => 4,
            'apothecary' => true,
            'positions' => [
                'troll' => 2,
                'bombardier' => 1,
                'looney' => 1,
                'fanatic' => 1,
                'pogoer' => 1,
                'goblin' => 6,
            ],
        ];

        $team = $this->loadTeamByArray($data);
        $em->persist($team);
        $em->flush();
    }

    public function getDependencies()
    {
        return array(
            CoachFixtures::class,
            ChampionshipFixtures::class,
        );
    }

    public static function getGroups(): array
    {
        return ['teams'];
    }
}