<?php

namespace Obblm\Core\Resources;

use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Helper\RuleHelper;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SkillsQuantityValidator extends ConstraintValidator
{
    private $ruleHelper;
    private $helper = null;
    private $total = 0;
    private $max = ['total' => 0];
    private $count = ['total' => 0];

    public function __construct(RuleHelper $ruleHelper)
    {
        $this->ruleHelper = $ruleHelper;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof SkillsQuantity) {
            throw new UnexpectedTypeException($constraint, SkillsQuantity::class);
        }
        if (!$value instanceof TeamVersion) {
            throw new UnexpectedTypeException($value, TeamVersion::class);
        }
        $this->max['total'] = $value->getTeam()->getCreationOption('skills_allowed.total') ?: 0;
        $this->initializeMaxValues($value, $constraint->context);

        $this->helper = $this->ruleHelper->getHelper($value->getTeam());
        foreach ($value->getNotDeadPlayerVersions() as $playerVersion) {
            if (count($playerVersion->getAdditionalSkills()) > $value->getTeam()->getCreationOption('skills_allowed.max_skills_per_player')) {
                $this->context->buildViolation($constraint->limitByPlayerMessage)
                    ->setParameter('{{ limit }}', $value->getTeam()->getCreationOption('skills_allowed.max_skills_per_player'))
                    ->addViolation();

                return;
            }
            foreach ($playerVersion->getAdditionalSkills() as $skillKey) {
                $skill = $this->helper->getSkill($skillKey);
                $context = $this->helper->getSkillContextForPlayerVersion($playerVersion, $skill);
                ++$this->count['total'];
                if ($this->count['total'] > $this->max['total']) {
                    $this->context->buildViolation($constraint->limitMessage)
                        ->setParameter('{{ limit }}', $this->max['total'])
                        ->addViolation();

                    return;
                }
                if (isset($this->max[$context])) {
                    ++$this->count[$context];
                    $limit = $this->max[$context] ?: 0;
                    if ($this->count[$context] > $limit) {
                        $this->context->buildViolation($constraint->limitByTypeMessage)
                            ->setParameter('{{ type }}', $context)
                            ->setParameter('{{ limit }}', $limit)
                            ->addViolation();

                        return;
                    }
                }
            }
        }
    }

    private function initializeMaxValues(TeamVersion $value, array $context)
    {
        $this->total = 0;
        foreach ($context as $key) {
            if (is_string($key)) {
                $this->max[$key] = $value->getTeam()->getCreationOption("skills_allowed.$key") ?: 0;
                $this->count[$key] = 0;
                $this->total += $value->getTeam()->getCreationOption("skills_allowed.$key") ?: 0;
            }
        }
    }
}
