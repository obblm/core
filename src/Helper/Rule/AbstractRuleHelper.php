<?php

namespace Obblm\Core\Helper\Rule;

use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Form\Player\ActionType;
use Obblm\Core\Form\Player\InjuryType;
use Obblm\Core\Helper\CoreTranslation;
use Obblm\Core\Helper\Rule\Traits\AbstractInducementRuleTrait;
use Obblm\Core\Helper\Rule\Traits\AbstractPlayerRuleTrait;
use Obblm\Core\Helper\Rule\Traits\AbstractTeamRuleTrait;
use Obblm\Core\Traits\ClassNameAsKeyTrait;

abstract class AbstractRuleHelper extends RuleConfigBuilder implements RuleHelperInterface
{
    use ClassNameAsKeyTrait,
        AbstractTeamRuleTrait,
        AbstractPlayerRuleTrait,
        AbstractInducementRuleTrait;

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
        return $this->getAttachedRule()->getTemplate();
    }

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
}
