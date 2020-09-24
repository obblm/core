<?php

namespace Obblm\Core\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Obblm\Championship\Entity\Encounter;
use Obblm\Core\Helper\Rule\Inducement\Inducement;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class InducementsQuantityValidator extends ConstraintValidator
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof InducementsQuantity) {
            throw new UnexpectedTypeException($constraint, InducementsQuantity::class);
        }
        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, Encounter::class);
        }

        $count = [];

        // Validate each inducement
        foreach ($value as $i => $inducement) {
            /** @var Inducement $inducement */
            if (!isset($count[$inducement->getKey()])) {
                $count[$inducement->getKey()] = 0;
            }
            $count[$inducement->getKey()]++;
            if ($count[$inducement->getKey()] > $inducement->getMax()) {
                $type = $this->translator->trans($inducement->getTranslationKey(), [], $inducement->getTranslationDomain());
                $this->context->buildViolation($constraint->limitMessage)
                    ->setParameter('{{ type }}', $type)
                    ->setParameter('{{ limit }}', $inducement->getMax())
                    ->addViolation();
            }
        }

        // Validate quantity of star players
        $value = new ArrayCollection($value);
        $criteria = (Criteria::create());
        $criteria->where(Criteria::expr()->eq('type', 'star_players'));
        $criteria->orderBy(['value' => 'ASC']);

        $star_players = $value->matching($criteria);

        if ($star_players->count() > $constraint->helper->getMaxStarPlayers()) {
            $type = $this->translator->trans($inducement->getTranslationType(), [], $inducement->getTranslationDomain());
            $this->context->buildViolation($constraint->limitMessage)
                ->setParameter('{{ type }}', $type)
                ->setParameter('{{ limit }}', $constraint->helper->getMaxStarPlayers())
                ->addViolation();
        }
    }
}