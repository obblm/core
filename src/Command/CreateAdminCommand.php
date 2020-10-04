<?php

namespace Obblm\Core\Command;

use Doctrine\ORM\EntityManagerInterface;
use Obblm\Core\Entity\Coach;
use Obblm\Core\Event\RegisterCoachEvent;
use Obblm\Core\Security\Roles;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateAdminCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'obblm:coach:create-admin';
    /** @var EntityManagerInterface */
    private $em;
    /** @var SymfonyStyle */
    private $io;
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
        $this->setDescription('Creates a new OBBLM Administrator.')
            ->setHelp('This command will add a new administrator.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title('Create a new OBBLM Administrator');
        $this->io->caution('Be carefull, this new user will have the highest right on the application');
        $continue = $this->io->confirm('Are you sure you want to continue ?', true);
        if($continue) {
            $coachRepository = $this->em->getRepository(Coach::class);
            $this->io->section('User informations');
            $username = $this->io->ask('User login', null, function ($username) use ($coachRepository) {
                if (empty($username)) {
                    throw new \RuntimeException('The login cannot be empty.');
                }
                if ($coachRepository->findOneByUsername($username)) {
                    throw new \RuntimeException('This login is allready used.');
                }
                return (string) $username;
            });
            $email = $this->io->ask('User email', null, function ($email) use ($coachRepository) {
                if (empty($email)) {
                    throw new \RuntimeException('The email cannot be empty.');
                }
                if ($coachRepository->findOneByEmail($email)) {
                    throw new \RuntimeException('This email is allready used.');
                }
                return (string) $email;
            });
            $password = $this->io->askHidden('User password', function ($password) {
                if (empty($password)) {
                    throw new \RuntimeException('Password cannot be empty.');
                }

                return $password;
            });
            $active = $this->io->confirm('Activate the user (or send him an activation email)', false);
            $coach = (new Coach());
            $password = $this->passwordEncoder->encodePassword($coach, $password);
            $coach
                ->setUsername($username)
                ->setEmail($email)
                ->setPassword($password)
                ->setActive($active)
                ->setLocale('en')
                ->setRoles([Roles::ADMIN])
                ;
            if(!$active) {
                $coach
                    ->setHash(hash('sha256', $coach->getEmail()));
                $registration = new RegisterCoachEvent($coach);
                $this->dispatcher->dispatch($registration, RegisterCoachEvent::NAME);
            }
            $this->io->success("The coach $username has been created !");
            $this->em->persist($coach);
            $this->em->flush();
            return 1;
        }
        $this->io->text('Aborted.');
        return 0;
    }
}
