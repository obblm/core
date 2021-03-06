<?php

namespace Obblm\Core\DataFixtures;

use Obblm\Core\Entity\Coach;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Obblm\Core\Security\Roles;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CoachFixtures extends Fixture
{
    private $encoder;
    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const MANAGER_USER_REFERENCE = 'manager-user';
    public const COACH_USER_REFERENCE = 'coach-user';

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $em)
    {
        $coach = (new Coach())
            ->setEmail('admin@obblm.com')
            ->setUsername('admin');
        $password = $this->encoder->encodePassword($coach, 'admin');
        $coach->setPassword($password);
        $coach->setRoles([
            Roles::ADMIN
        ]);
        $em->persist($coach);
        $this->addReference(self::ADMIN_USER_REFERENCE, $coach);
        $coach = (new Coach())
            ->setEmail('manager@obblm.com')
            ->setUsername('manager');
        $password = $this->encoder->encodePassword($coach, 'manager');
        $coach->setPassword($password);
        $coach->setRoles([
            Roles::MANAGER
        ]);
        $em->persist($coach);
        $this->addReference(self::MANAGER_USER_REFERENCE, $coach);
        $coach = (new Coach())
            ->setEmail('coach@obblm.com')
            ->setUsername('coach');
        $password = $this->encoder->encodePassword($coach, 'coach');
        $coach->setPassword($password);
        $coach->setRoles([
            Roles::COACH
        ]);
        $em->persist($coach);
        $this->addReference(self::COACH_USER_REFERENCE, $coach);

        $em->flush();
    }
}
