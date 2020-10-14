<?php

namespace Obblm\Core\Controller;

use Obblm\Core\Entity\Rule;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Security\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/rules", name="obblm_rules")
 */
class RulesController extends AbstractController
{
    /**
     * @Route("", name="")
     */
    public function list():Response
    {
        $this->denyAccessUnlessGranted(Roles::COACH);

        $rules = $this->getDoctrine()->getRepository(Rule::class)->findAll();

        return $this->render('@ObblmCore/rules/list.html.twig', ['rules' => $rules]);
    }

    /**
     * @Route("/{rule}", name="_detail")
     */
    public function detail(Rule $rule, RuleHelper $ruleHelper):Response
    {
        $this->denyAccessUnlessGranted(Roles::COACH);

        $helper = $ruleHelper->getHelper($rule);

        return $this->render('@ObblmCore/rules/detail.html.twig', [
            'rule' => $rule,
            'rosters' => $helper->getRosters(),
        ]);
    }

    /**
     * @Route("/{rule}/roster/{rosterKey}", name="_roster_sheet")
     */
    public function rosterSheet(Rule $rule, string $rosterKey, RuleHelper $ruleHelper):Response
    {
        $this->denyAccessUnlessGranted(Roles::COACH);

        $helper = $ruleHelper->getHelper($rule);
        $roster = $helper->getRosters()->get($rosterKey);

        return $this->render('@ObblmCore/rules/roster_sheet.html.twig', [
            'rule' => $rule,
            'roster' => $roster,
        ]);
    }

    /**
     * @Route("/{rule}/star-players", name="_star_players")
     */
    public function starPlayers(Rule $rule):Response
    {
        $this->denyAccessUnlessGranted(Roles::COACH);

        return $this->render('@ObblmCore/rules/star-players.html.twig', [
            'rule' => $rule,
        ]);
    }

    /**
     * @Route("/{rule}/skills", name="_skills")
     */
    public function skills(Rule $rule):Response
    {
        $this->denyAccessUnlessGranted(Roles::COACH);

        return $this->render('@ObblmCore/rules/skills.html.twig', [
            'rule' => $rule,
        ]);
    }
}
