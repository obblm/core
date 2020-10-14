<?php

namespace Obblm\Core\Validator\Constraints\Team;

use Obblm\Core\Contracts\InducementInterface;
use Obblm\Core\Contracts\PositionInterface;
use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\Player;
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
    /** @var RuleHelperInterface */
    private $helper;
    private $count = [];

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

        /** @var TeamVersion $value */
        $team  = $value->getTeam();
        $this->helper = $this->ruleHelper->getHelper($team);

        $this->validatePlayers($value, $constraint);
    }

    private function validatePlayers(TeamVersion $value, Composition $constraint)
    {
        foreach ($value->getNotDeadPlayerVersions() as $version) {
            if ($version->getPlayer()->getPosition()) {
                $this->validatePlayerPosition($version->getPlayer(), $constraint);
            }
        }
    }

    private function validatePlayerPosition(Player $player, Composition $constraint)
    {
        $element = $this->helper->getPlayerPosition($player);

        /* To prevent bugs from constraint messages */
        if (!$element instanceof Translatable) {
            throw new UnexpectedTypeException($element, Translatable::class);
        }

        $this->positionCount($element, $constraint);

        if ($element instanceof StarPlayer) {
            $this->inducementsCount($element, $constraint);
        }
    }

    /**
     * Validate limit of positions in the team
     * @param PositionInterface $element
     * @param Composition $constraint
     */
    private function positionCount(PositionInterface $element, Composition $constraint)
    {
        $limit = $element->getMax();
        $position = $element->getKey();

        isset($this->count[$position]) ? $this->count[$position]++ : $this->count[$position] = 1;
        if ($this->count[$position] > $limit) {
            $this->context->buildViolation($constraint->limitMessage)
                ->setParameter('{{ limit }}', $limit)
                ->setParameter('{{ player_type }}', $this->translator->trans($element->getName(), [], $element->getTranslationDomain()))
                ->addViolation();
        }
    }

    /**
     * Validate limit of star players, mercenaries, etc. in the team
     * @param InducementInterface $element
     * @param Composition $constraint
     */
    private function inducementsCount(InducementInterface $element, Composition $constraint)
    {
        $position = $element->getTypeKey();
        $limit = $this->helper->getMaxStarPlayers();
        isset($this->count[$position]) ? $this->count[$position]++ : $this->count[$position] = 1;
        if ($this->count[$position] > $limit) {
            $this->context->buildViolation($constraint->limitMessage)
                ->setParameter('{{ limit }}', $limit)
                ->setParameter('{{ player_type }}', $this->translator->trans($element->getType(), [], $element->getType()->getTranslationDomain()))
                ->addViolation();
        }
    }
}
