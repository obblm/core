<?php

namespace Obblm\Core\Validator\Constraints\Team;

use Obblm\Core\Contracts\RuleHelperInterface;
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
    /** @var RuleHelperInterface */
    protected $helper;

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

        $this->helper = $this->ruleHelper->getHelper($value->getTeam());

        // Skills cost included in team value
        $this->addValueValidator($value);

        // Skills quantity
        $this->addOtherValidator(
            new SkillsQuantityValidator($this->ruleHelper),
            new SkillsQuantity($this->generateSkillContexts($value)),
            $value
        );
    }

    private function addValueValidator($value)
    {
        if ($value->getTeam()->getCreationOption('skills_allowed') &&
            $value->getTeam()->getCreationOption('skills_allowed.choice') == AdditionalSkills::NOT_FREE) {
            $this->helper->applyTeamExtraCosts($value, true);
            $this->addOtherValidator(new ValueValidator($this->ruleHelper), new Value(), $value);
        }
    }

    private function generateSkillContexts($value):array
    {
        $skillContext = [];
        if ($value->getTeam()->getCreationOption('skills_allowed.single') !== null) {
            $skillContext[] = 'single';
        }
        if ($value->getTeam()->getCreationOption('skills_allowed.double') !== null) {
            $skillContext[] = 'double';
        }
        return $skillContext;
    }

    private function addOtherValidator(ConstraintValidator $validator, Constraint $constraint, $value)
    {
        $validator->initialize($this->context);
        $validator->validate($value, $constraint);
    }
}
