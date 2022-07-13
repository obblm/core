<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\Dashboard;

use Obblm\Core\Domain\Security\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="obblm.dashboard")
 */
class IndexController extends AbstractController
{
    public function __invoke(): Response
    {
        $this->denyAccessUnlessGranted(Roles::COACH);

        return $this->render('@ObblmCoreApplication/dashboard/index.html.twig');
    }
}
