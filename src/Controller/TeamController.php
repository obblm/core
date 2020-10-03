<?php

namespace Obblm\Core\Controller;

use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\Team;
use Obblm\Core\Event\TeamVersionEvent;
use Obblm\Core\Form\Team\EditTeamType;
use Obblm\Core\Helper\TeamHelper;
use Obblm\Core\Security\Roles;
use Obblm\Core\Security\Voter\TeamVoter;
use Obblm\Core\Service\FileTeamUploader;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TeamController
 * @package Obblm\Core\Controller
 *
 * @Route("/teams", name="obblm_team")
 */
class TeamController extends AbstractTeamController
{
    /**
     * @Route("/", name="_mine")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(Roles::COACH);

        $teams = $this->getUser()->getTeams();

        return $this->render('@ObblmCore/team/index.html.twig', [
            'teams' => $teams
        ]);
    }
    /**
     * @Route("/create", name="_create")
     */
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted(Roles::COACH);

        return $this->render('@ObblmCore/form/team/rules-selector.html.twig', [
        ]);
    }
    /**
     * @Route("/create/from-rule/{rule}", name="_create_rule")
     */
    public function createFromRule(Rule $rule, Request $request): Response
    {
        $this->denyAccessUnlessGranted(Roles::COACH);

        $team = (new Team())
            ->setRule($rule)
            ->setCoach($this->getUser());
        return $this->createAndComputeTeamForm($team, $request);
    }

    /**
     * @Route("/{team}", name="_detail")
     */
    public function detail(Team $team): Response
    {
        $this->denyAccessUnlessGranted(TeamVoter::VIEW, $team);
        return $this->render('@ObblmCore/team/detail.html.twig', [
            'version' => TeamHelper::getLastVersion($team),
        ]);
    }

    /**
     * @Route("/{team}/pdf", name="_pdf")
     */
    public function generatePdf(Team $team, Pdf $pdf): Response
    {
        $this->denyAccessUnlessGranted(TeamVoter::VIEW, $team);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('@ObblmCore/team/detail-pdf.html.twig', [
            'version' => TeamHelper::getLastVersion($team),
        ]);
        $pdf->setOption('orientation', 'landscape');
        $pdf->setOption('disable-javascript', true);
        $pdf->setOption('title', $team->getName());

        $fileName =  urlencode($team->getName()) . '.pdf';

        return new PdfResponse($pdf->getOutputFromHtml($html), $fileName, null, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/{team}/edit", name="_edit")
     */
    public function edit(Team $team, Request $request, EventDispatcherInterface $dispatcher): Response
    {
        $this->denyAccessUnlessGranted(TeamVoter::EDIT, $team);

        $form = $this->createForm(EditTeamType::class, TeamHelper::getLastVersion($team));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $version = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $dispatcher->dispatch(new TeamVersionEvent($version), TeamVersionEvent::PRE_SAVE);
            $dispatcher->dispatch(new TeamVersionEvent($version), TeamVersionEvent::TREASURE_BASE);
            $em->persist($version);
            $em->flush();
            $this->addFlash(
                'success',
                'Your changes were saved!'
            );
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
    public function delete(Team $team, Request $request, FileTeamUploader $uploader): Response
    {
        $this->denyAccessUnlessGranted(TeamVoter::DELETE, $team);

        $confirm = $request->get('confirm');
        if ($confirm !== null) {
            if ($confirm == 1) {
                $em = $this->getDoctrine()->getManager();
                $uploader->setObjectSubDirectory($team->getId());
                $uploader->removeOldFile();
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
