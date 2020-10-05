<?php

namespace Obblm\Core\Command;

use Doctrine\ORM\EntityManagerInterface;
use Obblm\Core\Entity\Coach;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

abstract class AbstractAdminCommand extends Command
{
    protected static $description = 'Command description.';
    protected static $help = 'Command help.';

    /** @var EntityManagerInterface */
    private $em;
    /** @var SymfonyStyle */
    protected $io;
    private $passwordEncoder;
    private $dispatcher;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->dispatcher = $dispatcher;
        parent::__construct();
    }

    protected function configure():void
    {
        $this->setDescription($this::$description)
            ->setHelp($this::$help);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function dispatch(object $event, string $eventName):object
    {
        return $this->dispatcher->dispatch($event, $eventName);
    }

    protected function confirmContinue():bool
    {
        return $this->io->confirm('Are you sure you want to continue ?', true);
    }

    protected function askUsername():string
    {
        $coachRepository = $this->em->getRepository(Coach::class);
        return $this->io->ask('User login', null, function ($username) use ($coachRepository) {
            if (empty($username)) {
                throw new \RuntimeException('The login cannot be empty.');
            }
            if ($coachRepository->findOneByUsername($username)) {
                throw new \RuntimeException('This login is allready used.');
            }
            return (string) $username;
        });
    }

    protected function askEmail():string
    {
        $coachRepository = $this->em->getRepository(Coach::class);
        return $this->io->ask('User email', null, function ($email) use ($coachRepository) {
            if (empty($email)) {
                throw new \RuntimeException('The email cannot be empty.');
            }
            if ($coachRepository->findOneByEmail($email)) {
                throw new \RuntimeException('This email is allready used.');
            }
            return (string) $email;
        });
    }

    protected function askPassword():string
    {
        $password = $this->io->askHidden('User password', function ($password) {
            if (empty($password)) {
                throw new \RuntimeException('Password cannot be empty.');
            }

            return $password;
        });
        return $this->passwordEncoder->encodePassword(new Coach(), $password);
    }

    protected function saveCoach(Coach $coach)
    {
        $this->em->persist($coach);
        $this->em->flush();
    }

    protected function getChoice():string
    {
        $choice = $this->io->choice('Search him by login or email ', ['login', 'email']);

        if ($choice !== 'login' && $choice !== 'email') {
            throw new \RuntimeException("Something went wrong.");
            return 0;
        }

        return $choice;
    }

    protected function getCoachByLoginOrEmail():Coach
    {
        $choice = $this->getChoice();

        $coachRepository = $this->em->getRepository(Coach::class);
        if ($choice === 'login') {
            return $this->io->ask('User login', null, function ($username) use ($coachRepository) {
                if (empty($username)) {
                    throw new \RuntimeException('The login cannot be empty.');
                }
                $coach = $coachRepository->findOneByUsername($username);
                if (!$coach) {
                    throw new \RuntimeException("This login doesn't exist.");
                }
                return $coach;
            });
        }
        return $this->io->ask('User email', null, function ($email) use ($coachRepository) {
            if (empty($email)) {
                throw new \RuntimeException('The email cannot be empty.');
            }
            $coach = $coachRepository->findOneByEmail($email);
            if (!$coach) {
                throw new \RuntimeException("This email doesn't exist.");
            }
            return $coach;
        });
    }
}
