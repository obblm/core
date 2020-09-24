<?php

namespace Obblm\Core\Helper\Rule;

use Doctrine\Common\Collections\Criteria;
use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\Team;
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

    public function getAvailableStarPlayers(Team $team):array
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->andX(
            $this->getInducementExpression([
                'type' => 'star_players',
                'roster' => $team->getRoster()
            ])
        ));
        return $this->getInducementTable()->matching($criteria)->toArray();
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
        $rule_key = $this->getAttachedRule()->getRuleKey();
        $fields = $this->rule['fields'];
        foreach ($fields as $field_key => $field) {
            $field_label = CoreTranslation::getFieldKey($rule_key, $field_key);
            $weather[$field_label] = [];
            foreach ($field['weather'] as $field_weather) {
                $label = CoreTranslation::getWeatherKey($rule_key, $field_key, $field_weather);
                $value = join(CoreTranslation::TRANSLATION_GLUE, [$rule_key, 'default', $field_weather]);
                $weather[$field_label][$label] = $value;
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
            ->setType($inducement->getType()->getTranslationKey())
            ->setName($inducement->getTranslationKey())
            ->addVersion($version);
        return $player;
    }

    public function createStarPlayerAsPlayer(string $key, int $number):Player
    {
        $rule_key = $this->getAttachedRule()->getRuleKey();

        $star_player = $this->getStarPlayer($key);
        if (isset($star_player['multi_parts']) && $star_player['multi_parts']) {
            throw new \Exception('You cannot create a player with a multiple parts InducementInterface');
        }
        $version = (new PlayerVersion())
            ->setCharacteristics($star_player['characteristics'])
            ->setValue($star_player['cost']);
        if ($star_player['skills']) {
            $version->setSkills($star_player['skills']);
        }
        $player = (new Player())
            ->setNumber($number)
            ->setType(CoreTranslation::getStarPlayerTitle($rule_key))
            ->setName(CoreTranslation::getStarPlayerName($rule_key, $key))
            ->addVersion($version);
        return $player;
    }

    use AbstractInducementRuleTrait;
}
