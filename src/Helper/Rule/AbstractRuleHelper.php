<?php

namespace Obblm\Core\Helper\Rule;

use Exception;
use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Exception\NoVersionException;
use Obblm\Core\Form\Player\ActionType;
use Obblm\Core\Form\Player\InjuryType;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Service\PlayerService;
use Obblm\Core\Traits\ClassNameAsKeyTrait;
use Obblm\Core\Validator\Constraints\TeamValue;

abstract class AbstractRuleHelper implements RuleHelperInterface {
    use ClassNameAsKeyTrait;

    protected $attachedRule;
    protected $rule = [];
    protected $injuries = [];

    /****************
     * COMPLIER PASS
     ****************/
    /**
     * @param Rule $rule
     * @return $this
     */
    public function attachRule(Rule $rule):RuleHelperInterface {
        $this->attachedRule = $rule;
        $this->rule = $rule->getRule();
        $this->prepareInjuriesTable();
        return $this;
    }

    /**
     * @return Rule
     */
    public function getAttachedRule():Rule {
        return $this->attachedRule;
    }

    protected function prepareInjuriesTable() {
        foreach($this->rule['injuries'] as $key => $injury) {
            $label = RuleHelper::composeTranslationInjuryKey($this->getAttachedRule()->getRuleKey(), $key);
            $effect_label = RuleHelper::composeTranslationInjuryEffect($this->getAttachedRule()->getRuleKey(), $key);
            if(isset($injury['to'])) {
                for($i = $injury['from']; $i <= $injury['to']; $i++) {
                    $this->injuries[$i] = (object) ['value' => $i, 'label' => $label, 'effect_label' => $effect_label, 'effects' => $injury['effects']];
                }
            } else {
                $this->injuries[$injury['from']] = (object) ['value' => $injury['from'], 'label' => $label, 'effect_label' => $effect_label, 'effects' => $injury['effects']];
            }
        }
    }

    /**********************
     * APPLICATION METHODS
     *********************/

    /**
     * @return string
     */
    public function getInjuriesFormClass():string {
        return InjuryType::class;
    }

    /**
     * @return string
     */
    public function getActionsFormClass():string {
        return ActionType::class;
    }

    /**
     * @return string
     */
    public function getTemplateKey():string {
        return $this->getKey();
    }

    /****************************
     * TEAM INFORMATION METHODS
     ***************************/
    /**
     * Get Max Team Cost
     *
     * @return int
     */
    public function getMaxTeamCost():int {
        return ($this->rule['max_team_cost']) ? $this->rule['max_team_cost'] : TeamValue::LIMIT;
    }

    /**
     * @param Team $team
     * @return int
     * @throws \Exception
     */
    public function getRerollCost(Team $team):int {
        return (int) $this->rule['rosters'][$team->getRoster()]['options']['reroll_cost'];
    }

    /**
     * @param Team $team
     * @return int
     */
    public function getApothecaryCost(Team $team):int {
        return (int) $this->rule['sidelines_cost']['apothecary'];
    }

    /**
     * @param Team $team
     * @return int
     */
    public function getCheerleadersCost(Team $team):int {
        return (int) $this->rule['sidelines_cost']['cheerleaders'];
    }

    /**
     * @param Team $team
     * @return int
     */
    public function getAssistantsCost(Team $team):int {
        return (int) $this->rule['sidelines_cost']['assistants'];
    }

    /**
     * @param Team $team
     * @return int
     */
    public function getPopularityCost(Team $team):int {
        return (int) $this->rule['sidelines_cost']['popularity'];
    }

    /**
     * @param Team $team
     * @return bool
     * @throws \Exception
     */
    public function couldHaveApothecary(Team $team):bool {
        return (bool) $this->rule['rosters'][$team->getRoster()]['options']['can_have_apothecary'];
    }

    public function calculateTeamRate(TeamVersion $version):?int {
        return $this->calculateTeamValue($version) / 10000;
    }

    public function calculateTeamValue(TeamVersion $version, bool $excludeDisposable = false):int {
        $team_cost = 0;

        // Players
        foreach($version->getTeam()->getNotDeadPlayers() as $basePlayer) {
            $player = (new PlayerVersion());
            try {
                $player = PlayerService::getLastVersion($basePlayer);
            }
            catch(NoVersionException $e) { // It's a new player !
                $basePlayer->addVersion($player);
                $version->addPlayerVersion($player);
                $this->setPlayerDefaultValues($player);
            }
            if(!$player->isMissingNextGame() && !($this->playerIsDisposable($player) && $excludeDisposable)) {
                $team_cost += $player->getValue();
            }
        }
        // Sidelines
        $team_cost += $version->getRerolls() * $this->getRerollCost($version->getTeam());
        $team_cost += $version->getAssistants() * $this->getAssistantsCost($version->getTeam());
        $team_cost += $version->getCheerleaders() * $this->getCheerleadersCost($version->getTeam());
        $team_cost += $version->getPopularity() * $this->getPopularityCost($version->getTeam());
        $team_cost += ($version->getApothecary()) ? $this->getApothecaryCost($version->getTeam()) : 0;

        return $team_cost;
    }

    public function playerIsDisposable(PlayerVersion $playerVersion):bool {
        return in_array('disposable', $playerVersion->getSkills());
    }

    /**
     *
     */

    public function getTeamAvailablePlayerTypes(Team $team) {
        return $this->getAvailablePlayerTypes($team->getRoster());
    }
    public function getAvailablePlayerTypes(string $roster):array {
        return $this->rule['rosters'][$roster]['players'];
    }
    public function getAvailablePlayerKeyTypes(string $roster):array {
        return array_keys($this->getAvailablePlayerTypes($roster));
    }

    /***************
     * MISC METHODS
     **************/
    public function getInjuriesTable():array {
        return $this->injuries;
    }

    public function getInjury($key):?object {
        if (!isset($this->injuries[$key])) {
            throw new Exception('No Injury found for ' . $key);
        }
        return $this->injuries[$key];
    }

    public function getSppLevel(PlayerVersion $version):?string {
        foreach($this->rule['experience'] as $start => $level) {
            if($version->getSpp() < $start) return $last;
            $last = $level;
        }
        return $this->rule['experience'][0];
    }
    public function setPlayerDefaultValues(PlayerVersion $version): ?PlayerVersion {
        /**
         * -characteristics: []
         * -skills: []
         * -spp_level: null
         * -value: null
         */
        list($rule_key, $roster, $type) = explode('.', $version->getPlayer()->getType());
        $base = $this->getAttachedRule()->getRule()['rosters'][$roster]['players'][$type];

        $version->setCharacteristics([
            'ma' => $base['ma'],
            'st' => $base['st'],
            'ag' => $base['ag'],
            'av' => $base['av']
        ])
            ->setActions([
                'td' => 0,
                'cas' => 0,
                'pas' => 0,
                'int' => 0,
                'mvp' => 0,
            ])
            ->setSkills(($base['skills'] ?? []))
            ->setValue($base['cost'])
            ->setSppLevel($this->getSppLevel($version));

        return $version;
    }
}
