<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\League;

use Obblm\Core\Application\Form\League\BaseLeagueForm;
use Obblm\Core\Domain\Command\League\EditLeagueCommand;
use Obblm\Core\Domain\Model\League;
use Obblm\Core\Domain\Security\Roles;
use Obblm\Core\Domain\Service\League\LeagueService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/leagues/edit/{league}", name="obblm.league.edit")
 */
class EditController extends AbstractController
{
    public function __invoke(League $league, LeagueService $leagueService, Request $request): Response
    {
        if (!$this->isGranted(Roles::ADMIN)) {
            return $this->redirectToRoute('obblm.dashboard');
        }

        $form = $this->createForm(BaseLeagueForm::class, [
            'name' => $league->getName(),
            'admin' => $league->getAdmin(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $command = EditLeagueCommand::fromArray($data, (string) $league->getId());
            $leagueService->create($command);

            return $this->redirectToRoute('obblm.league.list');
        }

        return $this->render('@ObblmCoreApplication/league/edit.html.twig', ['form' => $form->createView(), 'league' => $league]);
    }
}
