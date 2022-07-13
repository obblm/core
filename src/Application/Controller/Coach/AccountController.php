<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\Coach;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/account", name="obblm.account")
 */
class AccountController extends AbstractController
{
    public function __invoke(): Response
    {
        // TODO: Implement __invoke() method.
        return new Response();
    }
}
