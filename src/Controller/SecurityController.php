<?php

namespace Obblm\Core\Controller;

use Obblm\Core\Entity\Coach;
use Obblm\Core\Event\ActivateCoachEvent;
use Obblm\Core\Event\RegisterCoachEvent;
use Obblm\Core\Form\Security\RegistrationForm;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\VarDumper\VarDumper;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="obblm_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('obblm_dashboard');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@ObblmCore/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/{_locale}", name="obblm_locale_switch")
     */
    public function localeSwitch()
    {
        return $this->redirectToRoute('obblm_dashboard');
    }

    /**
     * @Route("/logout", name="obblm_logout")
     */
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EventDispatcherInterface $dispatcher):Response
    {
        $coach = new Coach();
        $form = $this->createForm(RegistrationForm::class, $coach);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($coach, $coach->getPlainPassword());
            $coach->setPassword($password)
                ->setHash(hash('sha256', $coach->getEmail()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($coach);

            $event = new RegisterCoachEvent($coach);
            $dispatcher->dispatch($event, RegisterCoachEvent::NAME);

            $entityManager->flush();

            $this->addFlash(
                'success',
                'obblm.flash.account.created'
            );
            return $this->redirectToRoute('obblm_login');
        }

        return $this->render('@ObblmCore/security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/activate/{hash}", name="activate_account")
     */
    public function activate(string $hash, EventDispatcherInterface $dispatcher):Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        /** @var ?Coach $coach */
        $coach = $entityManager->getRepository(Coach::class)->findOneByHash($hash);

        if (!$coach) {
            $this->addFlash(
                'error',
                'obblm.flash.account.not_found_by_hash'
            );

            return $this->redirectToRoute('obblm_login');
        }

        if (!$coach->isActive()) {
            $coach->setActive(true);
            $entityManager->persist($coach);

            $event = new ActivateCoachEvent($coach);
            $dispatcher->dispatch($event, ActivateCoachEvent::NAME);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'obblm.flash.account.activated'
            );

            return $this->redirectToRoute('obblm_login');
        }

        $this->addFlash(
            'success',
            'obblm.flash.account.already_activated'
        );

        return $this->redirectToRoute('obblm_login');
    }
}
