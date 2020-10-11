<?php

namespace Obblm\Core\Validator\Constraints\Team;

use Obblm\Core\Contracts\InducementInterface;
use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Exception\UnexpectedTypeException;
use Obblm\Core\Helper\Rule\Inducement\StarPlayer;
use Obblm\Core\Helper\Rule\Translatable;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Helper\TeamHelper;
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

        if ($value instanceof Team) {
            $value = TeamHelper::getLastVersion($value);
        }

        if (!$value instanceof TeamVersion) {
            throw new UnexpectedTypeException($value, TeamVersion::class);
        }

        $count = [];
        /** @var TeamVersion $value */
        $team  = $value->getTeam();
        $helper = $this->ruleHelper->getHelper($team);
        foreach ($value->getNotDeadPlayerVersions() as $version) {
            if ($version->getPlayer()->getPosition()) {
                $element = $helper->getPlayerPosition($version->getPlayer());
                $limit = $element->getMax();
                $position = $element->getKey();
                if (!$element instanceof Translatable) {
                    throw new UnexpectedTypeException($element, Translatable::class);
                }
                isset($count[$position]) ? $count[$position]++ : $count[$position] = 1;
                if ($count[$position] > $limit) {
                    $this->context->buildViolation($constraint->limitMessage)
                        ->setParameter('{{ limit }}', $limit)
                        ->setParameter('{{ player_type }}', $this->translator->trans($element->getName(), [], $element->getTranslationDomain()))
                        ->addViolation();
                }
                // Inducements
                if ($element instanceof StarPlayer) {
                    $position = $element->getTypeKey();
                    $limit = $helper->getMaxStarPlayers();
                    isset($count[$position]) ? $count[$position]++ : $count[$position] = 1;
                    if ($count[$position] > $limit) {
                        $this->context->buildViolation($constraint->limitMessage)
                            ->setParameter('{{ limit }}', $limit)
                            ->setParameter('{{ player_type }}', $this->translator->trans($element->getType(), [], $element->getType()->getTranslationDomain()))
                            ->addViolation();
                    }
                }
            }
        }
    }
}
