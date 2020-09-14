<?php

namespace Obblm\Core\Controller\Manager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ManagerController
 * @package Obblm\Core\Controller\Manager
 *
 * @Route("/manage")
 */
class ManagerController extends AbstractController {
    /**
     * @Route("/")
     */
    public function index() {
        $this->denyAccessUnlessGranted('OBBLM_USER');
        return $this->render('@ObblmCore/todo.html.twig', []);
    }
}
