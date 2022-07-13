<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class RegisterController.
 *
 * @Route("/forgot-password", name="obblm.forgot_password")
 */
class ForgotPasswordController extends AbstractController
{
    public function __invoke(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
    }
}
