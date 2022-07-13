<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\Team;

use Obblm\Core\Domain\Model\Team;
use Obblm\Core\Domain\Security\Voter\TeamVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/teams/{team}", name="obblm.team.view")
 * @ParamConverter("team")
 */
class ViewController extends AbstractController
{
    public function __invoke(Team $team)
    {
        $this->denyAccessUnlessGranted(TeamVoter::VIEW, $team);
        $version = $team->getVersions()->last();

        return $this->render('@ObblmCoreApplication/team/detail.html.twig', ['team' => $team, 'version' => $version]);
    }
}
