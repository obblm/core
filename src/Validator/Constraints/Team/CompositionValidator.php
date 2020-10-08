<?php

namespace Obblm\Core\Validator\Constraints\Team;

use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Helper\CoreTranslation;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Helper\TeamHelper;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class CompositionValidator extends ConstraintValidator
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
        if (!$constraint instanceof Composition) {
            throw new UnexpectedTypeException($constraint, Composition::class);
        }

        if ($value instanceof TeamVersion)
        {
            $value->getNotDeadPlayerVersions();
        }

        if (!$value instanceof TeamVersion) {
            throw new UnexpectedTypeException($value, TeamVersion::class);
        }
        if ($value instanceof Team) {
            $value = TeamHelper::getLastVersion($value);
        }
        $count = [];

        /** @var TeamVersion $value */
        $team  = $value->getTeam();
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
