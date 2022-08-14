<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\League;

use Obblm\Core\Domain\Model\League;
use Obblm\Core\Domain\Security\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/leagues/{league}", name="obblm.league.detail")
 */
class DetailController extends AbstractController
{
    public function __invoke(League $league): Response
    {
        $this->denyAccessUnlessGranted(Roles::ADMIN);

        return $this->render('@ObblmCoreApplication/league/detail.html.twig', ['league' => $league]);
    }
}
