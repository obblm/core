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

class PromoteCoachCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'obblm:coach:promote';
    /** @var EntityManagerInterface */
    private $em;
    /** @var SymfonyStyle */
    private $io;
    private $passwordEncoder;
    private $dispatcher;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure():void
    {
        $this->setDescription('Promotes an existing user to OBBLM Administrator.')
            ->setHelp('This command will promote an existing user to the administrator role.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title('Promote a new OBBLM Administrator');
        $this->io->caution('Be carefull, this new user will have the highest right on the application');
        $continue = $this->io->confirm('Are you sure you want to continue ?', true);
        if($continue) {
            $coachRepository = $this->em->getRepository(Coach::class);

            $choice = $this->io->choice('Search him by login or email ', ['login', 'email']);

            if($choice !== 'login' && $choice !== 'email') {
                throw new \RuntimeException("Something went wrong.");
                return 0;
            }
            elseif($choice === 'login') {
                $coach = $this->io->ask('User login', null, function ($username) use ($coachRepository) {
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
            elseif ($choice === 'email') {
                $coach = $this->io->ask('User email', null, function ($email) use ($coachRepository) {
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
            /** @var Coach $coach */
            $continue = $this->io->confirm("Are you sure you want to promote {$coach->getUsername()} ?", true);
            if($continue) {
                $coach
                    ->setRoles([Roles::ADMIN]);
                $this->em->persist($coach);
                $this->em->flush();
                $this->io->success("The coach {$coach->getUsername()} has been promoted !");
                return 1;
            }
        }
        $this->io->text('Aborted.');
        return 0;
    }
}
