<?php

namespace Obblm\Core\Controller;

use Obblm\Core\Entity\Team;
use Obblm\Core\Form\Team\TeamRulesSelectorForm;
use Obblm\Core\Helper\TeamHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractTeamController extends AbstractController
{
    /** @var TeamHelper */
    protected $teamHelper;

    public function __construct(TeamHelper $teamHelper)
    {
        $this->teamHelper = $teamHelper;
    }

    protected function createAndComputeTeamForm(Team $team, Request $request):Response
    {
        $form = $this->createForm(TeamRulesSelectorForm::class, $team);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $this->teamHelper->createNewTeamVersion($team);
            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();
            return $this->redirectToRoute('obblm_team_detail', ['team' => $team->getId()]);
        }

        return $this->render('@ObblmCore/form/team/create.rules-choice.html.twig', [
            'form' => $form->createView(),
            'rule' => $team->getRule(),
        ]);
    }
}
