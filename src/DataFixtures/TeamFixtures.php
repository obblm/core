<?php

namespace Obblm\Core\DataFixtures;

use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TeamFixtures extends Fixture implements DependentFixtureInterface
{
    public const TEAM_REFERENCE_RULE = 'team_by_rule';

    public function load(ObjectManager $em)
    {
        $rule = $em->getRepository(Rule::class)->findOneBy(['rule_key' => 'lrb6']);
        $team2 = (new Team())
            ->setName('Team test by rule')
            ->setRoster('dark_elf')
            ->setCoach($this->getReference(CoachFixtures::COACH_USER_REFERENCE))
            ->setRule($rule);
        $em->persist($team2);
        $this->addReference(self::TEAM_REFERENCE_RULE, $team2);

        $em->flush();
    }

    public function getDependencies()
    {
        return array(
            CoachFixtures::class,
        );
    }
}
