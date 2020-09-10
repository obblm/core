<?php

namespace Obblm\Core\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class FrontendController extends AbstractController {
    /**
     * @Route("/", name="home")
     */
    public function home(SerializerInterface $serializer) {
        $response = $this->render('@ObblmCore/dashboard/index.html.twig', [
            //'user' => $serializer->serialize($this->getUser(), 'json'),
        ]);
        // cache for 3600 seconds
        $response->setSharedMaxAge(3600);

        // (optional) set a custom Cache-Control directive
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}
