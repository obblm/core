<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\Locale;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/locale/{_locale}", name="obblm.locale.switch")
 */
class SwitcherController extends AbstractController
{
    public function __invoke(Request $request): Response
    {
        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else {
            // if no explicit locale has been set on this request, use one from the session
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }

        if ($request->headers->get('referer')) {
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->redirectToRoute('obblm.dashboard');
    }
}
