<?php

namespace Obblm\Core\Helper\Rule;

use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Form\Player\ActionType;
use Obblm\Core\Form\Player\InjuryType;
use Obblm\Core\Service\PlayerService;
use Obblm\Core\Service\RuleService;
use Obblm\Core\Service\TeamService;
use Obblm\Core\Traits\ClassNameAsKeyTrait;
use Exception;
use Obblm\Core\Validator\Constraints\TeamValue;

abstract class AbstractRuleHelper implements RuleHelperInterface {
    use ClassNameAsKeyTrait;

    protected $attachedRule;
    protected $injuries = [];

    public function getActionsFormClass():string {
        return ActionType::class;
    }
    public function getInjuriesFormClass():string {
        return InjuryType::class;
    }
    public function getTemplateKey():string {
        return $this->getKey();
    }
    public function attachRule(Rule $rule):self {
        $this->attachedRule = $rule;
        $this->prepareInjuriesTable();
        return $this;
    }
    public function getAttachedRule():Rule {
        return $this->attachedRule;
    }

    protected function prepareInjuriesTable() {
        $rule = $this->getAttachedRule()->getRule();
        foreach($rule['injuries'] as $key => $injury) {
            $label = RuleService::composeTranslationInjuryKey($this->getAttachedRule()->getRuleKey(), $key);
            $effect_label = RuleService::composeTranslationInjuryEffect($this->getAttachedRule()->getRuleKey(), $key);
            if(isset($injury['to'])) {
                for($i = $injury['from']; $i <= $injury['to']; $i++) {
                    $this->injuries[$i] = (object) ['value' => $i, 'label' => $label, 'effect_label' => $effect_label, 'effects' => $injury['effects']];
                }
            } else {
                $this->injuries[$injury['from']] = (object) ['value' => $injury['from'], 'label' => $label, 'effect_label' => $effect_label, 'effects' => $injury['effects']];
            }
        }
    }

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
        $rule = $this->getAttachedRule()->getRule();
        $last = $rule['experience'][0];
        foreach($rule['experience'] as $start => $level) {
            if($version->getSpp() < $start) return $last;
            $last = $level;
        }
        return $rule['experience'][0];
    }

    public function calculateTeamValue(TeamVersion $version, bool $excludeDisposable = false):int {
        $team_cost = 0;

        // Players
        foreach($version->getTeam()->getNotDeadPlayers() as $basePlayer) {
            $player = PlayerService::getLastVersion($basePlayer);
            if(!$player->isMissingNextGame() && !($this->playerIsDisposable($player) && $excludeDisposable)) {
                $team_cost += $player->getValue();
            }
        }
        // Sidelines
        $team_cost += $version->getRerolls() * TeamService::getRerollCost($version->getTeam());
        $team_cost += $version->getAssistants() * TeamService::getAssistantsCost($version->getTeam());
        $team_cost += $version->getCheerleaders() * TeamService::getCheerleadersCost($version->getTeam());
        $team_cost += $version->getPopularity() * TeamService::getPopularityCost($version->getTeam());
        $team_cost += ($version->getApothecary()) ? TeamService::getApothecaryCost($version->getTeam()) : 0;

        return $team_cost;
    }
    public function calculateTeamRate(TeamVersion $version):?int {
        return $this->calculateTeamValue($version) / 10000;
    }
    public function playerIsDisposable(PlayerVersion $playerVersion):bool {
        if(in_array('disposable', $playerVersion->getSkills())) {
            return true;
        }

        return false;
    }
    public function getMaxTeamCost():int {
        return $this->getAttachedRule()->getMaxTeamCost() ?? TeamValue::LIMIT;
    }
    public function setDefaultValues(PlayerVersion $version): ?PlayerVersion {
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
