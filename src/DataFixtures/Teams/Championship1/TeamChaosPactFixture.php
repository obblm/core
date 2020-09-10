<?php

namespace Obblm\Core\DataFixtures\Teams\Championship1;

use Obblm\Core\DataFixtures\ChampionshipFixtures;
use Obblm\Core\DataFixtures\Teams\CoachFixtures;
use Obblm\Core\DataFixtures\Teams\TeamFixtureTrait;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TeamChaosPactFixture extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    use TeamFixtureTrait;

    const COACH_NUMBER = 4;

    public function load(ObjectManager $em)
    {
        $this->setChampionship($this->getReference(ChampionshipFixtures::CHAMPIONSHIP_REFERENCE))
            ->setCoach($this->getReference( CoachFixtures::COACH_USER_REFERENCE . '-' . self::COACH_NUMBER));

        $data = [
            'name' => 'Team Chaos Pact',
            'roster' => 'chaos_pact',
            'rerolls' => 2,
            'apothecary' => true,
            'positions' => [
                'minotaur' => 1,
                'troll' => 1,
                'ogre' => 1,
                'dark_elf' => 1,
                'goblin' => 1,
                'skaven' => 1,
                'marauder' => 5,
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