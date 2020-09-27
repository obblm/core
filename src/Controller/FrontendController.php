<?php

namespace Obblm\Core\Controller;

use Obblm\Core\Security\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(name="obblm_")
 */
class FrontendController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function home(TranslatorInterface $translator):Response
    {
        $this->denyAccessUnlessGranted(Roles::COACH);

        $response = $this->render('@ObblmCore/dashboard/index.html.twig');
        // cache for 3600 seconds
        $response->setSharedMaxAge(3600);

        // (optional) set a custom Cache-Control directive
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}
