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

class RevokeCoachCommand extends AbstractAdminCommand
{
    /** @var string */
    protected static $defaultName = 'obblm:coach:revoke';
    protected static $description = 'Remoke the administrator role to an existing user.';
    protected static $help = 'This command will revoke the administrator role of an existing user.';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->io->title('Revoke an OBBLM Administrator');
        $this->io->caution('Be carefull, this new user will not have anymore the highest right on the application');
        if($this->confirmContinue()) {

            $coach = $this->getCoachByLoginOrEmail();

            $continue = $this->io->confirm("Are you sure you want to revoke {$coach->getUsername()} ?", true);
            if($continue) {
                if(in_array(Roles::ADMIN, $coach->getRoles())) {
                    $coach
                        ->setRoles([Roles::COACH]);
                    $this->saveCoach($coach);
                }
                $this->io->success("The coach {$coach->getUsername()} has been revoked !");
                return 1;
            }
        }
        $this->io->text('Aborted.');
        return 0;
    }
}
