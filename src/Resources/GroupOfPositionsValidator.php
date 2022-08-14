<?php

namespace Obblm\Core\Resources;

use Obblm\Core\Domain\Validator\Constraints\Team\GroupOfPositions;
use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Helper\RuleHelper;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class GroupOfPositionsValidator extends ConstraintValidator
{
    private $ruleHelper;
    private $translator;

    public function __construct(RuleHelper $ruleHelper, TranslatorInterface $translator)
    {
        $this->ruleHelper = $ruleHelper;
        $this->translator = $translator;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof GroupOfPositions) {
            throw new UnexpectedTypeException($constraint, GroupOfPositions::class);
        }

        if ($value instanceof Team) {
            $value = $value = $value->getLastVersion();
        }

        if (!$value instanceof TeamVersion) {
            throw new UnexpectedTypeException($value, TeamVersion::class);
        }

        $count = [];
        /** @var TeamVersion $value */
        $team = $value->getTeam();
        $helper = $this->ruleHelper->getHelper($team);
        foreach ($value->getNotDeadPlayerVersions() as $version) {
            if ($version->getPlayer()->getPosition()) {
                $position = $version->getPlayer()->getPosition();
                $basePosition = $helper->getRoster($team)->getPosition($position);
                isset($count[$position]) ? $count[$position]++ : $count[$position] = 1;
                if ($count[$position] > $basePosition->getMax()) {
                    $this->context->buildViolation($constraint->limitMessage)
                        ->setParameter('{{ limit }}', $basePosition->getMax())
                        ->setParameter('{{ player_type }}', $this->translator->trans($basePosition->getName(), [], $basePosition->getTranslationDomain()))
                        ->addViolation();
                }
            }
        }
    }
}