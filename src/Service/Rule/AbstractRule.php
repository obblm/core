<?php

namespace Obblm\Core\Service\Rule;

use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Form\Encounter\ActionType;
use Obblm\Core\Form\Encounter\InjuryType;
use Obblm\Core\Service\PlayerService;
use Obblm\Core\Service\RuleService;
use Obblm\Core\Service\TeamService;
use Obblm\Core\Traits\ClassNameAsKeyTrait;
use Exception;

abstract class AbstractRule implements RuleInterface {
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

    public function getSppLevel($spp):?string {
        $rule = $this->getAttachedRule()->getRule();
        $last = $rule['experience'][0];
        foreach($rule['experience'] as $start => $level) {
            if($spp < $start) return $last;
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
        $team_cost += $version->getTeam()->getRerolls() * TeamService::getRerollCost($version->getTeam());
        $team_cost += $version->getTeam()->getAssistants() * TeamService::getAssistantsCost($version->getTeam());
        $team_cost += $version->getTeam()->getCheerleaders() * TeamService::getCheerleadersCost($version->getTeam());
        $team_cost += $version->getTeam()->getPopularity() * TeamService::getPopularityCost($version->getTeam());
        $team_cost += ($version->getTeam()->getApothecary()) ? TeamService::getApothecaryCost($version->getTeam()) : 0;

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
}
