<?php

namespace Obblm\Core\Controller;

use Obblm\Core\Entity\Team;
use Obblm\Core\Form\Team\TeamRulesSelectorForm;
use Obblm\Core\Helper\RuleHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractTeamController extends AbstractController {

    protected $ruleHelper;

    public function __construct(RuleHelper $ruleHelper) {
        $this->ruleHelper = $ruleHelper;
    }

    protected function createAndComputeTeamForm(Team $team, Request $request):Response
    {
        $form = $this->createForm(TeamRulesSelectorForm::class, $team);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()) {
            //$team = $form->getData();
            $this->ruleHelper->createNewTeamVersion($team);
            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();
            return $this->redirectToRoute('obblm_team_detail', ['team' => $team->getId()]);
        }

        return $this->render('@ObblmCore/form/team/create.rules-choice.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}