<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\Team;

use Obblm\Core\Application\Controller\ObblmAbstractController;
use Obblm\Core\Application\Form\Team\SelectRuleForm;
use Obblm\Core\Domain\Service\Rule\RuleService;
use Obblm\Core\Domain\Service\Team\TeamService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/teams/create", name="obblm.team.create")
 */
class CreateController extends ObblmAbstractController
{
    public function __invoke(TeamService $teamService, RuleService $ruleService, Request $request)
    {
        $form = $this->createForm(SelectRuleForm::class, null, [
            'rules' => $ruleService->findAllowedRules($this->getUser())
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            return $this->redirectToRoute('obblm.team.create.roster', ['rule' => $data['rule']->getId()]);
        }

        return $this->render('@ObblmCoreApplication/team/create.html.twig', ['form' => $form->createView()]);
    }
}
