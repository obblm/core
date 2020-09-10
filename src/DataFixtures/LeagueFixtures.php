<?php

namespace Obblm\Core\DataFixtures;

use Obblm\Core\Entity\League;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LeagueFixtures extends Fixture implements DependentFixtureInterface
{
    public const LEAGUE_REFERENCE = 'league';

    public function load(ObjectManager $em)
    {
        $league = (new League())
            ->setName('League test')
            ->setOwner($this->getReference(CoachFixtures::ADMIN_USER_REFERENCE));
        $em->persist($league);
        $this->addReference(self::LEAGUE_REFERENCE, $league);

        $em->flush();
    }

    public function getDependencies()
    {
        return array(
            CoachFixtures::class,
        );
    }
}