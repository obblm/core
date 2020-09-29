<?php

namespace Obblm\Core\Controller;

use Obblm\Core\Form\Coach\EditUserForm;
use Obblm\Core\Security\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/account", name="obblm_account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="")
     */
    public function editAccount(Request $request, UserPasswordEncoderInterface $passwordEncoder):Response
    {
        $coach = $this->getUser();
        $this->denyAccessUnlessGranted(Roles::COACH, $coach);

        $form = $this->createForm(EditUserForm::class, $this->getUser());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($data->getPlainPassword()) {
                $password = $passwordEncoder->encodePassword($coach, $coach->getPlainPassword());
                $coach->setPassword($password)
                    ->setHash(hash('sha256', $coach->getEmail()));
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($coach);
            $entityManager->flush();
        }

        return $this->render('@ObblmCore/account/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
