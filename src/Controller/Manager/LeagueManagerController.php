<?php

namespace Obblm\Core\Controller\Manager;

use Obblm\Core\Entity\League;
use Obblm\Core\Form\League\ManageLeagueForm;
use Obblm\Core\Security\Voter\LeagueVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ManagerController
 * @package Obblm\Core\Controller\Manager
 *
 * @Route("/manage/leagues")
 */
class LeagueManagerController extends AbstractController {
    /**
     * @Route("/", name="manage_leagues")
     */
    public function index() {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('@ObblmCore/todo.html.twig', []);
    }

    /**
     * @Route("/edit/{league}", name="manage_leagues_edit")
     */
    public function edit(League $league, Request $request, EntityManagerInterface $em) {
        $this->denyAccessUnlessGranted(LeagueVoter::EDIT, $league);

        $form = $this->createForm(ManageLeagueForm::class, $league);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($league);
            $em->flush();
            return $this->redirectToRoute('manage_leagues');
        }

        return $this->render('@ObblmCore/admin/leagues/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
