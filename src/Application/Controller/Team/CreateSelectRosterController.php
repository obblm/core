<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\Team;

use Obblm\Core\Application\Controller\ObblmAbstractController;
use Obblm\Core\Application\Form\Team\BaseTeamForm;
use Obblm\Core\Domain\Command\Team\CreateTeamCommand;
use Obblm\Core\Domain\Model\Rule;
use Obblm\Core\Domain\Security\Roles;
use Obblm\Core\Domain\Service\Rule\RuleService;
use Obblm\Core\Domain\Service\Team\TeamService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @Route("/teams/create/{rule}", name="obblm.team.create.roster")
 */
class CreateSelectRosterController extends ObblmAbstractController
{
    public function __invoke(Rule $rule, RuleService $ruleService, TeamService $teamService, Request $request)
    {
        $this->denyAccessUnlessGranted(Roles::COACH);

        $form = $this->createForm(BaseTeamForm::class, null, [
            'rule' => $ruleService->getHelper($rule),
            'choice_translation_domain' => $ruleService->getHelper($rule)->getKey(),
        ]);
        VarDumper::dump($ruleService->getHelper($rule));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data['rule'] = $rule;
            $data['coach'] = $this->getUser();
            $command = $this->commandFromArray(CreateTeamCommand::class, $data);
            $teamService->create($command);

            return $this->redirectToRoute('obblm.team.list');
        }

        return $this->render('@ObblmCoreApplication/team/create.html.twig', ['creation_context' => $rule, 'form' => $form->createView()]);
    }
}