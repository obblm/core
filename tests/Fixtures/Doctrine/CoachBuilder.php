<?php

declare(strict_types=1);

namespace Obblm\Core\Tests\Fixtures\Doctrine;

use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Service\Hash;
use Obblm\Core\Tests\Fixtures\BuilderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CoachBuilder extends AbstractDoctrineBuilder implements BuilderInterface
{
    protected ?string $email = null;
    protected ?string $username = null;
    protected ?string $password = null;

    public function build(): Coach
    {
        $encoder = $this->container->get(UserPasswordEncoderInterface::class);
        $coach = (new Coach())
            ->setUsername($this->username ? $this->username : random_bytes(8))
            ->setEmail($this->email ? $this->email : random_bytes(8).'@'.random_bytes(8).'.com')
            ->setPlainPassword($this->password ? $this->password : random_bytes(8))
            ->setActive(true);

        $coach->setPassword($encoder->encodePassword($coach, $coach->getPlainPassword()));
        $coach->setHash((new Hash())($coach->getEmail()));

        $em = $this->container->get('doctrine')->getManager();
        $em->persist($coach);
        $em->flush();

        return $coach;
    }
}
