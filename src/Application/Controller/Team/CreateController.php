<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\Team;

use Obblm\Core\Application\Form\Team\BaseTeamForm;
use Obblm\Core\Application\Form\Team\RuledTeamForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/teams/create", name="obblm.team.create")
 */
class CreateController extends AbstractController
{
    public function __invoke(Request $request)
    {
        $form = $this->createForm(RuledTeamForm::class);

        $form->handleRequest($request);

        return $this->render('@ObblmCoreApplication/team/create.html.twig', ['form' => $form->createView()]);
    }
}
