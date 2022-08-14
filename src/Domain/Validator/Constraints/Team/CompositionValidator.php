<?php

namespace Obblm\Core\Domain\Validator\Constraints\Team;

use Obblm\Core\Domain\Exception\UnexpectedTypeException;
use Obblm\Core\Domain\Model\Proxy\Inducement\StarPlayer;
use Obblm\Core\Domain\Model\Team;
use Obblm\Core\Domain\Model\TeamVersion;
use Obblm\Core\Domain\Model\Translatable;
use Obblm\Core\Domain\Service\Rule\RuleService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class CompositionValidator extends ConstraintValidator
{
    private $ruleService;
    private $translator;

    public function __construct(RuleService $ruleService, TranslatorInterface $translator)
    {
        $this->ruleService = $ruleService;
        $this->translator = $translator;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Composition) {
            throw new UnexpectedTypeException($constraint, Composition::class);
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
        $helper = $this->ruleService->getHelper($team);
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
