<?php

namespace Obblm\Core\Command;

use Doctrine\ORM\EntityManagerInterface;
use Obblm\Core\Entity\Coach;
use Obblm\Core\Event\RegisterCoachEvent;
use Obblm\Core\Security\Roles;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAdminCommand extends AbstractAdminCommand
{
    /** @var string */
    protected static $defaultName = 'obblm:coach:create-admin';
    protected static $description = 'Creates a new OBBLM Administrator.';
    protected static $help = 'This command will add a new administrator.';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->io->title('Create a new OBBLM Administrator');
        $this->io->caution('Be carefull, this new user will have the highest right on the application');

        if($this->confirmContinue()) {

            $this->io->section('User informations');
            $username = $this->askUsername();
            $email = $this->askEmail();
            $password = $this->askPassword();
            $active = $this->io->confirm('Activate the user (or send him an activation email)', false);

            $coach = (new Coach())
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
                $this->dispatch($registration, RegisterCoachEvent::NAME);
            }
            $this->saveCoach($coach);
            $this->io->success("The coach $username has been created !");
            return 1;
        }
        $this->io->text('Aborted.');
        return 0;
    }
}
