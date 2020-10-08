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

class GroupOfPositionsValidator extends ConstraintValidator
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
        if (!$constraint instanceof GroupOfPositions) {
            throw new UnexpectedTypeException($constraint, GroupOfPositions::class);
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

        $maxPositions = $this->getMaxPlayersByTypes($value->getTeam());
        foreach ($value->getNotDeadPlayerVersions() as $version) {
            if ($version->getPlayer()->getPosition()) {
                $playerType = $version->getPlayer()->getPosition();
                list($ruleKey, $roster, $type) = explode(CoreTranslation::TRANSLATION_GLUE, $playerType);
                $limit = $maxPositions[$type];
                isset($count[$type]) ? $count[$type]++ : $count[$type] = 1;
                if ($count[$type] > $limit) {
                    $translationName = CoreTranslation::getPlayerKeyType($ruleKey, $roster, $type);
                    $this->context->buildViolation($constraint->limitMessage)
                        ->setParameter('{{ limit }}', $limit)
                        ->setParameter('{{ player_type }}', $this->translator->trans($translationName, [], 'lrb6'))
                        ->addViolation();
                }
            }
        }
    }

    protected function getMaxPlayersByTypes(Team $team):array
    {
        $helper = $this->ruleHelper->getHelper($team);
        $maxPositions = [];

        if ($helper->getAvailablePlayerTypes($team->getRoster())) {
            $types = $helper->getAvailablePlayerTypes($team->getRoster());
            foreach ($types as $key => $type) {
                $maxPositions[$key] = $type['max'];
            }
        }

        return $maxPositions;
    }
}
