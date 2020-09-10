<?php

namespace Obblm\Core\Controller\Admin;

use Obblm\Core\Entity\Coach;
use Obblm\Core\Form\AdminUserForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserAdminController
 * @package Obblm\Core\Controller\Admin
 *
 * @Route("/admin/users")
 */
class UserAdminController extends AbstractController {
    /**
     * @Route("/", name="admin_users")
     */
    public function index(EntityManagerInterface $em) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $em->getRepository(Coach::class)
                    ->findAll();

        return $this->render('@ObblmCore/admin/users/index.html.twig', [
            'users' => $users
        ]);
    }
    /**
     * @Route("/add", name="admin_users_add")
     */
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new Coach();
        $form = $this->createForm(AdminUserForm::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password)->setPlainPassword('');
            $em->persist($user);
            $em->flush();
        }
        return $this->render('@ObblmCore/admin/users/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/edit/{user}", name="admin_users_edit")
     */
    public function edit(Coach $user, Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(AdminUserForm::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            if($user->getPlainPassword()) {
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password)->setPlainPassword('');
            }
            $em->persist($user);
            $em->flush();
        }
        return $this->render('@ObblmCore/admin/users/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
