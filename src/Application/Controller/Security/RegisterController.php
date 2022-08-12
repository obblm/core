<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\Security;

use Obblm\Core\Application\Controller\ObblmAbstractController;
use Obblm\Core\Application\Form\Security\RegistrationForm;
use Obblm\Core\Domain\Command\Coach\CreateCoachCommand;
use Obblm\Core\Domain\Service\Coach\CoachService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegisterController.
 *
 * @Route("/register", name="obblm.register")
 */
class RegisterController extends ObblmAbstractController
{
    public function __invoke(CoachService $coachService, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('obblm.dashboard');
        }

        $form = $this->createForm(RegistrationForm::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $command = $this->commandFromArray(CreateCoachCommand::class, $data);
            $coachService->create($command);
            $this->redirectToRoute('obblm.login');
        }

        return $this->render('@ObblmCoreApplication/security/register.html.twig', ['form' => $form->createView()]);
    }
}
