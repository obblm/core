<?php

namespace Obblm\Core\Domain\Validator\Constraints\Team;

use Obblm\Core\Domain\Model\Team;
use Obblm\Core\Domain\Model\TeamVersion;
use Obblm\Core\Domain\Service\Rule\RuleService;
use Obblm\Core\Resources\SkillsQuantity;
use Obblm\Core\Resources\SkillsQuantityValidator;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AdditionalSkillsValidator extends ConstraintValidator
{
    protected $ruleService;

    public function __construct(RuleService $ruleService)
    {
        $this->ruleService = $ruleService;
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
            $value = $value->getLastVersion();
        }

        $helper = $this->ruleService->getHelper($value->getTeam());

        // Skills cost included in team value
        if ($value->getTeam()->getCreationOption('skills_allowed') &&
            AdditionalSkills::NOT_FREE == $value->getTeam()->getCreationOption('skills_allowed.choice')) {
            $helper->applyTeamExtraCosts($value, true);
            $this->addOtherValidator(new ValueValidator($this->ruleService), new Value(), $value);
        }

        // Skills quantity
        $skillContext = [];
        if (null !== $value->getTeam()->getCreationOption('skills_allowed.single')) {
            $skillContext[] = 'single';
        }
        if (null !== $value->getTeam()->getCreationOption('skills_allowed.double')) {
            $skillContext[] = 'double';
        }
        $this->addOtherValidator(new SkillsQuantityValidator($this->ruleService), new SkillsQuantity($skillContext), $value);
    }

    private function addOtherValidator(ConstraintValidator $validator, Constraint $constraint, $value)
    {
        $validator->initialize($this->context);
        $validator->validate($value, $constraint);
    }
}
