<?php

namespace Obblm\Core\Controller;

use Obblm\Core\Entity\Championship;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\Team;
use Obblm\Core\Form\Team\EditTeamType;
use Obblm\Core\Form\Team\TeamRulesSelectorForm;
use Obblm\Core\Security\Voter\TeamVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
 * Class TeamController
 * @package Obblm\Core\Controller
 *
 * @Route("/teams")
 */
class TeamController extends AbstractController {
    /**
     * @Route("/", name="my_teams")
     */
    public function index(): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $teams = $this->getUser()->getTeams();

        return $this->render('@ObblmCore/team/index.html.twig', [
            'teams' => $teams
        ]);
    }
    /**
     * @Route("/create", name="team_create")
     */
    public function create(Request $request): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $team = new Team();
        $form = $this->createForm(TeamRulesSelectorForm::class, $team);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()) {
            if($team->getRule()) {
                return $this->redirectToRoute('team_create_rule', ['rule' => $team->getRule()->getId()]);
            }
            elseif($team->getChampionship()) {
                return $this->redirectToRoute('team_create_championship', ['championship' => $team->getChampionship()->getId()]);
            }
            else {
                throw new InvalidParameterException("Impossible to create the redirect route.");
            }
        }

        return $this->render('@ObblmCore/form/team/create.rules-choice.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/create/from-rule/{rule}", name="team_create_rule")
     */
    public function createFromRule(Rule $rule, Request $request): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $team = (new Team())
            ->setRule($rule)
            ->setCoach($this->getUser());
        $form = $this->createForm(TeamRulesSelectorForm::class, $team);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()) {
            $team = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();
            return $this->redirectToRoute('team_detail', ['team' => $team->getId()]);
        }

        return $this->render('@ObblmCore/form/team/create.rules-choice.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/create/for-championship/{championship}", name="team_create_championship")
     */
    public function createForChampionship(Championship $championship, Request $request): Response {
        $this->denyAccessUnlessGranted('championship.view', $championship);

        $team = (new Team())
            ->setChampionship($championship)
            ->setCoach($this->getUser());
        $form = $this->createForm(TeamRulesSelectorForm::class, $team);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()) {
            $team = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();
            return $this->redirectToRoute('team_detail', ['team' => $team->getId()]);
        }

        return $this->render('@ObblmCore/form/team/create.rules-choice.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{team}", name="team_detail")
     */
    public function detail(Team $team): Response {
        $this->denyAccessUnlessGranted(TeamVoter::VIEW, $team);
        return $this->render('@ObblmCore/team/detail.html.twig', [
            'team' => $team,
        ]);
    }
    /**
     * @Route("/{team}/edit", name="team_edit")
     */
    public function edit(Team $team, Request $request): Response {
        $this->denyAccessUnlessGranted(TeamVoter::EDIT, $team);

        $form = $this->createForm(EditTeamType::class, $team);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $team = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();
            return $this->redirectToRoute('team_detail', ['team' => $team->getId()]);
        }

        return $this->render('@ObblmCore/form/team/edit.html.twig', [
            'form' => $form->createView(),
            'team' => $team
        ]);
    }
    /**
     * @Route("/{team}/delete", name="team_delete")
     */
    public function delete(Team $team): Response {
        $this->denyAccessUnlessGranted(TeamVoter::EDIT, $team);
        return $this->render('@ObblmCore/todo.html.twig', []);
    }
}
