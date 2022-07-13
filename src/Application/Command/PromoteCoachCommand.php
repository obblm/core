<?php

namespace Obblm\Core\Application\Command;

use Obblm\Core\Domain\Security\Roles;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PromoteCoachCommand extends AbstractAdminCommand
{
    /** @var string */
    protected static $defaultName = 'obblm:coach:promote';
    protected static $description = 'Promotes an existing user to OBBLM Administrator.';
    protected static $help = 'This command will promote an existing user to the administrator role.';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->io->title('Promote a new OBBLM Administrator');
        $this->io->caution('Be carefull, this new user will have the highest right on the application');
        if ($this->confirmContinue()) {
            $coach = $this->getCoachByLoginOrEmail();
            $continue = $this->io->confirm("Are you sure you want to promote {$coach->getUsername()} ?", true);
            if ($continue) {
                $coach
                    ->setRoles([Roles::ADMIN]);
                $this->saveCoach($coach);
                $this->io->success("The coach {$coach->getUsername()} has been promoted !");
                return 1;
            }
        }
        $this->io->text('Aborted.');
        return 0;
    }
}
