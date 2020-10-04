<?php

namespace Obblm\Core\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Obblm\Core\Entity\Coach;
use Obblm\Core\Event\ActivateCoachEvent;
use Obblm\Core\Event\RegisterCoachEvent;
use Obblm\Core\Event\ResetPasswordCoachEvent;
use Obblm\Core\Form\Security\ForgotPasswordForm;
use Obblm\Core\Form\Security\PasswordConfirmType;
use Obblm\Core\Form\Security\RegistrationForm;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/", name="obblm_")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
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
     * @Route("/logout", name="logout")
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
                ->setHash($this->getHashFor($coach->getEmail()));

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
            $coach->setHash(null)->setActive(true);
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

    /**
     * @Route("/forgot-password", name="forgot_password")
     */
    public function forgotPassword(Request $request, EventDispatcherInterface $dispatcher):Response
    {
        $form = $this->createForm(ForgotPasswordForm::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            /** @var Coach $coach */
            $coach = $em->getRepository(Coach::class)->findOneByEmail($data['email']);
            if($coach) {
                $coach
                    ->setResetPasswordAt(new \DateTime())
                    ->setResetPasswordHash($this->getHashFor(random_bytes(10)));
                $em->persist($coach);
                $em->flush();
                $dispatcher->dispatch(new ResetPasswordCoachEvent($coach), ResetPasswordCoachEvent::NAME);

                $this->addFlash(
                    'success',
                    'obblm.flash.account.forgot_password.sent'
                );

                return $this->redirectToRoute('obblm_login');
            }
        }

        return $this->render('@ObblmCore/security/forgot-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset-password/{hash}", name="reset_password")
     */
    public function resetPassword(string $hash, Request $request, UserPasswordEncoderInterface $passwordEncoder, EventDispatcherInterface $dispatcher):Response
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Coach $coach */
        $coach = $em->getRepository(Coach::class)->findOneForPasswordReset($hash);

        if($coach) {
            $form = $this->createForm(PasswordConfirmType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $data = $form->getData();
                $password = $passwordEncoder->encodePassword($coach, $data['plainPassword']);
                $coach->setPassword($password)
                    ->setResetPasswordHash(null)
                    ->setResetPasswordAt(null);
                $em->persist($coach);
                $em->flush();
                $this->addFlash(
                    'success',
                    'obblm.flash.account.forgot_password.changed'
                );
                return $this->redirectToRoute('obblm_login');
            }

            return $this->render('@ObblmCore/security/reset-password.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        $this->addFlash(
            'error',
            'obblm.flash.account.forgot_password.not_found'
        );
        return $this->redirectToRoute('obblm_forgot_password');
    }

    private function getHashFor($value)
    {
        return hash('sha256', $value);
    }
}
