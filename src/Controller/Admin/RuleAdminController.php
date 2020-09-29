<?php

namespace Obblm\Core\Controller\Admin;

use Obblm\Core\Entity\Rule;
use Doctrine\ORM\EntityManagerInterface;
use Obblm\Core\Security\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RuleAdminController
 * @package Obblm\Core\Controller\Admin
 *
 * @Route("/admin/rules")
 */
class RuleAdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_rules")
     */
    public function index(EntityManagerInterface $em):Response
    {
        $this->denyAccessUnlessGranted(Roles::ADMIN);

        $rules = $em->getRepository(Rule::class)
            ->findAll();

        return $this->render('@ObblmCore/admin/rules/index.html.twig', [
            'rules' => $rules
        ]);
    }
}
