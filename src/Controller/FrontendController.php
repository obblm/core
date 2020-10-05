<?php

namespace Obblm\Core\Controller;

use Doctrine\Common\Collections\Criteria;
use Obblm\Core\Entity\Coach;
use Obblm\Core\Security\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="obblm_")
 */
class FrontendController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function home():Response
    {
        $this->denyAccessUnlessGranted(Roles::COACH);

        return $this->render('@ObblmCore/dashboard/index.html.twig');
    }

    /**
     * @Route("/{_locale}", name="locale_switch")
     */
    public function localeSwitch(Request $request)
    {
        if ($request->headers->get('referer')) {
            return $this->redirect($request->headers->get('referer'));
        }
        return $this->redirectToRoute('obblm_dashboard');
    }

    public function lastTeams($max = 5):Response
    {
        $this->denyAccessUnlessGranted(Roles::COACH);

        $user = $this->getUser();
        $teams = [];
        if ($user instanceof Coach) {
            $criteria = Criteria::create()
                ->setMaxResults($max)
                ->orderBy(['id' => 'DESC']);
            $teams = $user->getTeams()->matching($criteria);
        }

        return $this->render('@ObblmCore/dashboard/last-teams.html.twig', [
            'teams' => $teams,
        ]);
    }
}
