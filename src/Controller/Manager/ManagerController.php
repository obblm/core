<?php

namespace Obblm\Core\Controller\Manager;

use Obblm\Core\Security\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ManagerController
 * @package Obblm\Core\Controller\Manager
 *
 * @Route("/manage")
 */
class ManagerController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index():Response
    {
        $this->denyAccessUnlessGranted(Roles::MANAGER);
        return $this->render('@ObblmCore/todo.html.twig', []);
    }
}
