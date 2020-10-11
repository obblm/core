<?php

namespace Obblm\Core\Validator\Constraints\Team;

use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Helper\TeamHelper;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AdditionalSkillsValidator extends ConstraintValidator
{
    protected $ruleHelper;

    public function __construct(RuleHelper $ruleHelper)
    {
        $this->ruleHelper = $ruleHelper;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof AdditionalSkills) {
            throw new UnexpectedTypeException($constraint, AdditionalSkills::class);
        }
        if (!$value instanceof Team && !$value instanceof TeamVersion) {
            throw new UnexpectedTypeException($value, Team::class);
        }
        if ($value instanceof Team) {
            $value = TeamHelper::getLastVersion($value);
        }

        $helper = $this->ruleHelper->getHelper($value->getTeam());

        // Skills cost included in team value
        if ($value->getTeam()->getCreationOption('skills_allowed') &&
            $value->getTeam()->getCreationOption('skills_allowed.choice') == AdditionalSkills::NOT_FREE) {
            $helper->applyTeamExtraCosts($value, true);
            $this->addOtherValidator(new ValueValidator($this->ruleHelper), new Value(), $value);
        }

        // Skills quantity
        $skillContext = [];
        if ($value->getTeam()->getCreationOption('skills_allowed.single') !== null) {
            $skillContext[] = 'single';
        }
        if ($value->getTeam()->getCreationOption('skills_allowed.double') !== null) {
            $skillContext[] = 'double';
        }
        $this->addOtherValidator(new SkillsQuantityValidator($this->ruleHelper), new SkillsQuantity($skillContext), $value);
    }

    private function addOtherValidator(ConstraintValidator $validator, Constraint $constraint, $value)
    {
        $validator->initialize($this->context);
        $validator->validate($value, $constraint);
    }
}
