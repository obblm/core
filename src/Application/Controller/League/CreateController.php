<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\League;

use Obblm\Core\Application\Controller\ObblmAbstractController;
use Obblm\Core\Application\Form\League\BaseLeagueForm;
use Obblm\Core\Domain\Command\League\CreateLeagueCommand;
use Obblm\Core\Domain\Security\Roles;
use Obblm\Core\Domain\Service\League\LeagueService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/leagues/create", name="obblm.league.create")
 */
class CreateController extends ObblmAbstractController
{
    public function __invoke(LeagueService $leagueService, Request $request): Response
    {
        if (!$this->isGranted(Roles::ADMIN)) {
            return $this->redirectToRoute('obblm.dashboard');
        }

        $form = $this->createForm(BaseLeagueForm::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $command = $this->commandFromArray(CreateLeagueCommand::class, $data);
            $leagueService->create($command);

            return $this->redirectToRoute('obblm.league.list');
        }

        return $this->render('@ObblmCoreApplication/league/create.html.twig', ['form' => $form->createView()]);
    }
}
