<?php

namespace Obblm\Core\Domain\Validator\Constraints\Team;

use Obblm\Core\Domain\Model\Team;
use Obblm\Core\Domain\Model\TeamVersion;
use Obblm\Core\Domain\Service\Rule\RuleService;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValueValidator extends ConstraintValidator
{
    protected $ruleService;

    public function __construct(RuleService $ruleService)
    {
        $this->ruleService = $ruleService;
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
            $value = $value->getLastVersion();
        }

        $helper = $this->ruleService->getHelper($value->getTeam());
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
