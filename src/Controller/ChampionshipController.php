<?php

namespace Obblm\Core\Controller;

use Obblm\Core\Entity\Championship;
use Obblm\Core\Entity\Encounter;
use Obblm\Core\Entity\Team;
use Obblm\Core\Form\Encounter\EncounterForm;
use Obblm\Championship\Service\ChampionshipService;
use Obblm\Core\Service\RuleService;
use Obblm\Core\Service\TeamService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LeagueController
 * @package Obblm\Core\Controller
 *
 * @Route("/championships")
 */
class ChampionshipController extends AbstractController {
    /**
     * @Route("/", name="my_championships")
     */
    public function index() {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $championships = $this->getDoctrine()->getRepository(Championship::class)->findCoachAllowedChampionships();

        return $this->render('@ObblmCore/championship/index.html.twig', [
            'championships' => $championships
        ]);
    }
    /**
     * @Route("/{championship}", name="championship_detail")
     */
    public function detail(Championship $championship, ChampionshipService $championshipService) {
        $this->denyAccessUnlessGranted('championship.view', $championship);

        $versions = $championshipService
            ->getFormat($championship->getFormat())
            ->getOrderedTeamsWithTieBreaks($championship);
        if(count($versions) == 0 && $this->isGranted('championship.manage', $championship)) {
            $versions = array_map(function(Team $team) {
                return TeamService::getLastVersion($team);
            }, $championship->getTeams()->toArray());
        }

        return $this->render('@ObblmCore/championship/detail.html.twig', [
            'championship' => $championship,
            'teamVersions' => $versions,
        ]);
    }
    /**
     * @Route("/{championship}/team/{team}", name="championship_team")
     */
    public function championshipTeam(Championship $championship, Team $team) {
        $this->denyAccessUnlessGranted('championship.view', $championship);

        return $this->render('@ObblmCore/team/detail.html.twig', [
            'team' => $team,
        ]);
    }
    /**
     * @Route("/{championship}/encounter/add", name="championship_add_encounter")
     */
    public function addEncounter(Championship $championship, Request $request) {
        $this->denyAccessUnlessGranted('championship.add_encounter', $championship);

        $encounter = (new Encounter())
            ->setChampionship($championship)
            ->setCreatedAt(new DateTime());

        $form = $this->createForm(EncounterForm::class, $encounter, [
            'available_teams' => ChampionshipService::getFreeOfEncountersTeams($championship)
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($encounter);
            $em->flush();
            return $this->redirectToRoute('championship_edit_encounter', [
                'championship' => $championship->getId(),
                'encounter' => $encounter->getId(),
            ]);
        }

        return $this->render('@ObblmCore/form/encounter/add.html.twig', [
            'championship' => $championship,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{championship}/encounter/{encounter}", name="championship_view_encounter")
     */
    public function viewEncounter(Championship $championship, Encounter $encounter) {
        $this->denyAccessUnlessGranted('encounter.view', $encounter);

        return $this->render('@ObblmCore/encounter/detail.html.twig', [
            'championship' => $championship,
            'encounter' => $encounter
        ]);
    }
    /**
     * @Route("/{championship}/encounter/{encounter}/edit", name="championship_edit_encounter")
     */
    public function editEncounter(Championship $championship, Encounter $encounter, RuleService $ruleService, Request $request) {
        $this->denyAccessUnlessGranted('encounter.edit', $encounter);

        $form = $this->createForm(EncounterForm::class, $encounter, [
            'available_teams' => ChampionshipService::getFreeOfEncountersTeams($championship),
            'disable_teams' => true,
            'rule' => $ruleService->getRule($championship->getRule())
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach($encounter->getHomeActions() as $action) $encounter->addAction($action);
            foreach($encounter->getVisitorActions() as $action) $encounter->addAction($action);
            $em->persist($encounter);
            $em->flush();
            return $this->redirectToRoute('championship_view_encounter', [
                'championship' => $championship->getId(),
                'encounter' => $encounter->getId(),
            ]);
        }

        return $this->render('@ObblmCore/form/encounter/edit.html.twig', [
            'championship' => $championship,
            'form' => $form->createView(),
            'encounter' => $encounter
        ]);
    }
}
