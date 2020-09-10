<?php

namespace Obblm\Core\Controller;

use Obblm\Core\Entity\League;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LeagueController
 * @package Obblm\Core\Controller
 *
 * @Route("/leagues")
 */
class LeagueController extends AbstractController {
    /**
     * @Route("/", name="my_leagues")
     */
    public function index() {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $leagues = $this->getDoctrine()->getRepository(League::class)->findAll();

        return $this->render('@ObblmCore/league/index.html.twig', [
            'leagues' => $leagues
        ]);
    }
}
