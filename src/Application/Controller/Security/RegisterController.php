<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\Security;

use Obblm\Core\Application\Form\Security\RegistrationForm;
use Obblm\Core\Domain\Command\Coach\CreateCoachCommand;
use Obblm\Core\Domain\Service\Coach\CoachService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegisterController.
 *
 * @Route("/register", name="obblm.register")
 */
class RegisterController extends AbstractController
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
            $command = CreateCoachCommand::fromArray($data);
            $coachService->create($command);
            $this->redirectToRoute('obblm.login');
        }

        return $this->render('@ObblmCoreApplication/security/register.html.twig', ['form' => $form->createView()]);
    }
}
