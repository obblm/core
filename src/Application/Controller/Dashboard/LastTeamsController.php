<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\Dashboard;

use Doctrine\Common\Collections\Criteria;
use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Security\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class LastTeamsController extends AbstractController
{
    public function __invoke($max = 5): Response
    {
        $this->denyAccessUnlessGranted(Roles::COACH);

        $user = $this->getUser();
        $teams = [];
        if ($user instanceof Coach) {
            $criteria = Criteria::create()
                ->setMaxResults($max)
                ->orderBy(['id' => 'DESC']);
            $teams = $user->getTeams()->matching($criteria);
        }

        return $this->render('@ObblmCoreApplication/dashboard/last-teams.html.twig', [
            'teams' => $teams,
        ]);
    }
}
