<?php

namespace Obblm\Core\Controller;

use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\Team;
use Obblm\Core\Event\TeamVersionEvent;
use Obblm\Core\Form\Team\EditTeamType;
use Obblm\Core\Helper\TeamHelper;
use Obblm\Core\Security\Roles;
use Obblm\Core\Security\Voter\TeamVoter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TeamController
 * @package Obblm\Core\Controller
 *
 * @Route("/teams", name="obblm_team")
 */
class TeamController extends AbstractTeamController {
    /**
     * @Route("/", name="_mine")
     */
    public function index(): Response {
        $this->denyAccessUnlessGranted(Roles::COACH);

        $teams = $this->getUser()->getTeams();

        return $this->render('@ObblmCore/team/index.html.twig', [
            'teams' => $teams
        ]);
    }
    /**
     * @Route("/create", name="_create")
     */
    public function create(Request $request): Response {
        $this->denyAccessUnlessGranted(Roles::COACH);

        return $this->render('@ObblmCore/form/team/rules-selector.html.twig', [
        ]);
    }
    /**
     * @Route("/create/from-rule/{rule}", name="_create_rule")
     */
    public function createFromRule(Rule $rule, Request $request): Response {
        $this->denyAccessUnlessGranted(Roles::COACH);

        $team = (new Team())
            ->setRule($rule)
            ->setCoach($this->getUser());
        return $this->createAndComputeTeamForm($team, $request);
    }
    /**
     * @Route("/{team}", name="_detail")
     */
    public function detail(Team $team): Response {
        $this->denyAccessUnlessGranted(TeamVoter::VIEW, $team);
        return $this->render('@ObblmCore/team/detail.html.twig', [
            'version' => TeamHelper::getLastVersion($team),
        ]);
    }
    /**
     * @Route("/{team}/edit", name="_edit")
     */
    public function edit(Team $team, Request $request, EventDispatcherInterface $dispatcher): Response {
        $this->denyAccessUnlessGranted(TeamVoter::EDIT, $team);

        $form = $this->createForm(EditTeamType::class, TeamHelper::getLastVersion($team));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $version = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $dispatcher->dispatch(new TeamVersionEvent($version), TeamVersionEvent::PRE_SAVE);
            $em->persist($version);
            $em->flush();
            return $this->redirectToRoute('obblm_team_detail', ['team' => $team->getId()]);
        }

        return $this->render('@ObblmCore/form/team/edit.html.twig', [
            'form' => $form->createView(),
            'team' => $team
        ]);
    }
    /**
     * @Route("/{team}/delete", name="_delete")
     */
    public function delete(Team $team, Request $request): Response {
        $this->denyAccessUnlessGranted(TeamVoter::EDIT, $team);

        $confirm = $request->get('confirm');
        if($confirm !== null) {
            if ($confirm == 1) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($team);
                $em->flush();
                return $this->redirectToRoute('obblm_team_mine');
            }
            return $this->redirectToRoute('obblm_team_detail', ['team' => $team->getId()]);
        }

        return $this->render('@ObblmCore/form/team/delete.html.twig', [
            'team' => $team
        ]);
    }
}
