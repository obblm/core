<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\Team;

use Obblm\Core\Application\Form\Team\RuledTeamForm;
use Obblm\Core\Domain\Command\Team\CreateTeamCommand;
use Obblm\Core\Domain\Service\Team\TeamService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/teams/create", name="obblm.team.create")
 */
class CreateController extends AbstractController
{
    public function __invoke(TeamService $teamService, Request $request)
    {
        $form = $this->createForm(RuledTeamForm::class);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $data['coach'] = $this->getUser();
            $command = CreateTeamCommand::fromArray($data);
            $teamService->create($command);
        }

        return $this->render('@ObblmCoreApplication/team/create.html.twig', ['form' => $form->createView()]);
    }
}
