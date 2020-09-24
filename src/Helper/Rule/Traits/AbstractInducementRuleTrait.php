<?php

namespace Obblm\Core\Helper\Rule\Traits;

use Doctrine\Common\Collections\Criteria;
use Obblm\Core\Entity\Team;
use Obblm\Core\Exception\NotFoundRuleKeyExcepion;
use Obblm\Core\Helper\CoreTranslation;
use Obblm\Core\Helper\Rule\Inducement\Inducement;
use Obblm\Core\Helper\Rule\Inducement\InducementType;

/*********************
 * INDUCEMENT METHODS
 ********************/
trait AbstractInducementRuleTrait
{
    public function getMaxStarPlayers():int
    {
        return $this->rule['inducements']['star_players']['max'];
    }

    public function getInducements():array
    {
        $inducements = [];
        $ruleKey = $this->getAttachedRule()->getRuleKey();
        $availableInducements = $this->rule['inducements'];

        foreach ($availableInducements as $key => $value) {
            if ($key !== 'star_players') {
                $inducement = [
                    'type' => $this->getInducementType('inducements'),
                    'key' => join(CoreTranslation::TRANSLATION_GLUE, [$ruleKey, 'inducements', $key]),
                    'translation_domain' => $this->getAttachedRule()->getRuleKey(),
                    'translation_key' => CoreTranslation::getInducementName($ruleKey, $key),
                    'max' => $value['max'] ?? 0,
                    'value' => $value['cost'],
                ];
                $inducements[] = new Inducement($inducement);
            }
        }
        return $inducements;
    }

    public function getInducementsByTeamOptions(array $options):array
    {
        $inducements = [];
        $ruleKey = $this->getAttachedRule()->getRuleKey();
        $availableInducements = $this->rule['inducements'];

        foreach ($availableInducements as $key => $value) {
            if ($key !== 'star_players') {
                if ($options[$key]) {
                    $inducement = [
                        'type' => $this->getInducementType('inducements'),
                        'key' => join(CoreTranslation::TRANSLATION_GLUE, [$ruleKey, 'inducements', $key]),
                        'translation_domain' => $this->getAttachedRule()->getRuleKey(),
                        'translation_key' => CoreTranslation::getInducementName($ruleKey, $key),
                        'max' => $value['max'] ?? 0,
                        'value' => ($options[$key] === 'discount') ? $value['discounted_cost'] : $value['cost'],
                    ];
                    $inducements[] = new Inducement($inducement);
                }
            }
        }
        return $inducements;
    }

    public function getStarPlayers():array
    {
        return $this->rule['star_players'];
    }

    public function getStarPlayer(string $key):array
    {
        if (!isset($this->rule['star_players'][$key])) {
            throw new NotFoundRuleKeyExcepion($key);
        }
        return $this->rule['star_players'][$key];
    }
}
