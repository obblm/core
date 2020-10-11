<?php

namespace Obblm\Core\Validator\Constraints\Team;

use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Helper\TeamHelper;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValueValidator extends ConstraintValidator
{
    protected $ruleHelper;

    public function __construct(RuleHelper $ruleHelper)
    {
        $this->ruleHelper = $ruleHelper;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Value) {
            throw new UnexpectedTypeException($constraint, Value::class);
        }
        if (!$value instanceof Team && !$value instanceof TeamVersion) {
            throw new UnexpectedTypeException($value, Team::class);
        }
        if ($value instanceof Team) {
            $value = TeamHelper::getLastVersion($value);
        }

        $helper = $this->ruleHelper->getHelper($value->getTeam());
        $limit = $helper->getMaxTeamCost($value->getTeam());

        $helper->applyTeamExtraCosts($value, true);
        $teamCost = $helper->calculateTeamValue($value);

        if ($teamCost > $limit) {
            $this->context->buildViolation($constraint->limitMessage)
                ->setParameter('{{ limit }}', $limit)
                ->setParameter('{{ current }}', $teamCost)
                ->addViolation();
        }
    }
}