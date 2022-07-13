<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\League;

use Obblm\Core\Domain\Repository\LeagueRepositoryInterface;
use Obblm\Core\Domain\Security\Roles;
use Obblm\Core\Domain\Service\League\LeagueService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/leagues", name="obblm.league.list")
 */
class LeagueController extends AbstractController
{
    public function __invoke(LeagueService $leagueService): Response
    {
        $this->denyAccessUnlessGranted(Roles::ADMIN);

        $leagues = $leagueService->findAll();

        return $this->render('@ObblmCoreApplication/league/list.html.twig', ['leagues' => $leagues]);
    }
}
