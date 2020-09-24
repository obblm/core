<?php

namespace Obblm\Core\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package Obblm\Core\Controller\Admin
 *
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin")
     */
    public function index():Response
    {
        $this->denyAccessUnlessGranted('OBBLM_ADMIN');
        return $this->render('@ObblmCore/admin/index.html.twig', []);
    }
}
