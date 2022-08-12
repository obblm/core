<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\Team;

use Obblm\Core\Domain\Model\Team;
use Obblm\Core\Domain\Security\Roles;
use Obblm\Core\Domain\Service\Team\TeamService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/teams", name="obblm.team.list")
 */
class ListController extends AbstractController
{
    public function __invoke(TeamService $service)
    {
        $this->denyAccessUnlessGranted(Roles::COACH);
        $teams = $service->findByCoach($this->getUser());

        return $this->render('@ObblmCoreApplication/team/index.html.twig', ['teams' => $teams]);
    }
}
