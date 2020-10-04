<?php

namespace Obblm\Core\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Obblm\Core\Entity\Coach;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EntityExistsValidator extends ConstraintValidator
{
    private $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repository = $em->getRepository(Coach::class);
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof EntityExists) {
            throw new UnexpectedTypeException($constraint, EntityExists::class);
        }
        $coach = $this->repository->findOneByEmail($value);

        if (!$coach) {
            $this->context->buildViolation($constraint->notExistMessage)
                ->addViolation();
        }
    }
}
