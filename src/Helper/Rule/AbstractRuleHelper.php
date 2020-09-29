<?php

namespace Obblm\Core\Helper\Rule;

use Doctrine\Common\Collections\Criteria;
use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Form\Player\ActionType;
use Obblm\Core\Form\Player\InjuryType;
use Obblm\Core\Helper\CoreTranslation;
use Obblm\Core\Contracts\InducementInterface;
use Obblm\Core\Helper\Rule\Inducement\StarPlayer;
use Obblm\Core\Helper\Rule\Traits\AbstractInducementRuleTrait;
use Obblm\Core\Helper\Rule\Traits\AbstractPlayerRuleTrait;
use Obblm\Core\Helper\Rule\Traits\AbstractTeamRuleTrait;
use Obblm\Core\Traits\ClassNameAsKeyTrait;

abstract class AbstractRuleHelper extends RuleConfigBuilder implements RuleHelperInterface
{
    use ClassNameAsKeyTrait;

    protected $attachedRule;
    protected $rule = [];

    /****************
     * COMPLIER PASS
     ****************/
    /**
     * @param Rule $rule
     * @return $this
     */
    public function attachRule(Rule $rule):RuleHelperInterface
    {
        $this->setAttachedRule($rule);
        $this->build($rule->getRuleKey(), $rule->getRule());
        return $this;
    }

    public function getTransformedInducementsFor(string $roster)
    {
        $table = $this->getInducementTable();
        foreach ($table as $inducement) {
            if ($roster == 'halfling') {
                $inducement->setValue(10000);
            }
        }

        return $table;
    }

    /**********
     * CACHING
     *********/

    public function getAttachedRule():?Rule
    {
        return $this->attachedRule;
    }

    public function setAttachedRule(Rule $rule):RuleHelperInterface
    {
        $this->attachedRule = $rule;
        $this->rule = $rule->getRule();
        return $this;
    }

    /**********************
     * APPLICATION METHODS
     *********************/

    /**
     * @return string
     */
    public function getInjuriesFormClass():string
    {
        return InjuryType::class;
    }

    /**
     * @return string
     */
    public function getActionsFormClass():string
    {
        return ActionType::class;
    }

    /**
     * @return string
     */
    public function getTemplateKey():string
    {
        return $this->getKey();
    }

    use AbstractTeamRuleTrait;

    use AbstractPlayerRuleTrait;

    /***************
     * MISC METHODS
     **************/
    public function getWeatherChoices():array
    {
        $weather = [];
        $ruleKey = $this->getAttachedRule()->getRuleKey();
        $fields = $this->rule['fields'];
        foreach ($fields as $fieldKey => $field) {
            $fieldLabel = CoreTranslation::getFieldKey($ruleKey, $fieldKey);
            $weather[$fieldLabel] = [];
            foreach ($field['weather'] as $fieldWeather) {
                $label = CoreTranslation::getWeatherKey($ruleKey, $fieldKey, $fieldWeather);
                $value = join(CoreTranslation::TRANSLATION_GLUE, [$ruleKey, 'default', $fieldWeather]);
                $weather[$fieldLabel][$label] = $value;
            }
        }
        return $weather;
    }

    public function createInducementAsPlayer(InducementInterface $inducement, $number = 0):?Player
    {
        if (!$inducement instanceof StarPlayer) {
            return null;
        }
        $version = (new PlayerVersion())
            ->setCharacteristics($inducement->getCharacteristics())
            ->setValue($inducement->getValue());
        if ($inducement->getSkills()) {
            $version->setSkills($inducement->getSkills());
        }
        $player = (new Player())
            ->setNumber($number)
            ->setType($inducement->getType()->getName())
            ->setName($inducement->getName())
            ->addVersion($version);
        return $player;
    }

    public function createStarPlayerAsPlayer(string $key, int $number):Player
    {
        $ruleKey = $this->getAttachedRule()->getRuleKey();

        $starPlayer = $this->getStarPlayer($key);
        if (isset($starPlayer['multi_parts']) && $starPlayer['multi_parts']) {
            throw new \Exception('You cannot create a player with a multiple parts InducementInterface');
        }
        $version = (new PlayerVersion())
            ->setCharacteristics($starPlayer['characteristics'])
            ->setValue($starPlayer['cost']);
        if ($starPlayer['skills']) {
            $version->setSkills($starPlayer['skills']);
        }
        $player = (new Player())
            ->setNumber($number)
            ->setType(CoreTranslation::getStarPlayerTitle($ruleKey))
            ->setName(CoreTranslation::getStarPlayerName($ruleKey, $key))
            ->addVersion($version);
        return $player;
    }

    use AbstractInducementRuleTrait;
}
