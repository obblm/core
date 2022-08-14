<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\Team;

use Obblm\Core\Domain\Model\Team;
use Obblm\Core\Domain\Security\Voter\TeamVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/teams/{team}/rules", name="obblm.team.edit.rules")
 */
class EditRulesController extends AbstractController
{
    public function __invoke(Team $team)
    {
        $this->denyAccessUnlessGranted(TeamVoter::EDIT, $team);

        return $this->render('@ObblmCoreApplication/team/index.html.twig', ['teams' => $teams]);
    }
}
