<?php

namespace Obblm\Core\Helper\Rule;

use Doctrine\Common\Collections\ArrayCollection;
use Obblm\Core\Contracts\InducementInterface;
use Obblm\Core\Contracts\RosterInterface;
use Obblm\Core\Contracts\Rule\RuleBuilderInterface;
use Obblm\Core\Entity\Team;
use Obblm\Core\Helper\CoreTranslation;
use Obblm\Core\Helper\Rule\Config\ConfigResolver;
use Obblm\Core\Helper\Rule\Config\RuleConfigResolver;
use Obblm\Core\Helper\Rule\Inducement\Inducement;
use Obblm\Core\Helper\Rule\Inducement\InducementType;
use Obblm\Core\Helper\Rule\Inducement\MultipleStarPlayer;
use Obblm\Core\Helper\Rule\Inducement\StarPlayer;
use Obblm\Core\Helper\Rule\Roster\Roster;
use Obblm\Core\Helper\Rule\Skill\Skill;

class RuleConfigBuilder extends RuleConfigResolver implements RuleBuilderInterface
{
    /** @var ArrayCollection */
    private $injuries;
    /** @var ArrayCollection|Skill[] */
    private $skills;
    /** @var ArrayCollection|InducementType[] */
    private $inducementTypes;
    /** @var ArrayCollection */
    private $sppLevels;
    /** @var ArrayCollection|InducementInterface[] */
    private $inducements;
    /** @var ArrayCollection|RosterInterface[] */
    private $rosters;

    protected function build(string $ruleKey, array $rule)
    {
        $treeResolver = new ConfigResolver($this);
        $rule = $treeResolver->resolve($rule);
        $this->prepareInjuriesTable($ruleKey, $rule);
        $this->prepareSppTable($ruleKey, $rule);
        $this->prepareSkillsTable($ruleKey, $rule);
        $this->prepareInducementTypes($ruleKey);
        $this->prepareInducementTable($ruleKey, $rule);
        $this->prepareRosterTable($ruleKey, $rule);
    }

    public function getInjuries():ArrayCollection
    {
        return $this->injuries;
    }

    public function setInjuries(ArrayCollection $injuries):self
    {
        $this->injuries = $injuries;
        return $this;
    }

    public function getInducementTypes():ArrayCollection
    {
        return $this->inducementTypes;
    }

    public function setInducementTypes(ArrayCollection $inducementTypes):self
    {
        $this->inducementTypes = $inducementTypes;
        return $this;
    }

    public function getInducementType(string $type):InducementType
    {
        return $this->getInducementTypes()->get($type);
    }

    public function getSppLevels():ArrayCollection
    {
        return $this->sppLevels;
    }

    public function setSppLevels(ArrayCollection $sppLevels):self
    {
        $this->sppLevels = $sppLevels;
        return $this;
    }

    public function getInducementTable():ArrayCollection
    {
        return $this->inducements;
    }

    public function setInducementTable(ArrayCollection $inducements):self
    {
        $this->inducements = $inducements;
        return $this;
    }

    /** @return RosterInterface[]|ArrayCollection */
    public function getRosters():ArrayCollection
    {
        return $this->rosters;
    }

    public function getRoster(Team $team):RosterInterface
    {
        return $this->rosters->get($team->getRoster());
    }

    public function setRosters(ArrayCollection $rosters):self
    {
        $this->rosters = $rosters;
        return $this;
    }

    public function getSkills():ArrayCollection
    {
        return $this->skills;
    }

    public function setSkills(ArrayCollection $skills):self
    {
        $this->skills = $skills;
        return $this;
    }

    private function prepareInjuriesTable(string $ruleKey, array $rule)
    {
        $this->injuries = new ArrayCollection();
        foreach ($rule['injuries'] as $key => $injury) {
            $label = CoreTranslation::getInjuryKey($ruleKey, $key);
            $effectLabel = CoreTranslation::getInjuryEffect($ruleKey, $key);
            if (isset($injury['to'])) {
                for ($i = $injury['from']; $i <= $injury['to']; $i++) {
                    $this->injuries->set(
                        $i,
                        (object) ['value' => $i, 'label' => $label, 'effect_label' => $effectLabel, 'effects' => $injury['effects']]
                    );
                }
            } else {
                $this->injuries->set(
                    $injury['from'],
                    (object) ['value' => $injury['from'], 'label' => $label, 'effect_label' => $effectLabel, 'effects' => $injury['effects']]
                );
            }
        }
    }

    private function prepareInducementTypes(string $ruleKey)
    {
        $this->inducementTypes = new ArrayCollection();
        $this->inducementTypes->set('star_players', new InducementType([
            'key' => 'star_players',
            'name' => CoreTranslation::getStarPlayerTitle($ruleKey),
            'translation_domain' => $ruleKey,
        ]));
        $this->inducementTypes->set('inducements', new InducementType([
            'key' => 'inducements',
            'name' => CoreTranslation::getInducementTitle($ruleKey),
            'translation_domain' => $ruleKey,
        ]));
        $this->inducementTypes->set('mercenary', new InducementType([
            'key' => 'mercenary',
            'name' => CoreTranslation::getMercenaryTitle($ruleKey),
            'translation_domain' => $ruleKey,
        ]));
    }

    private function prepareSppTable(string $ruleKey, array $rule)
    {
        $spps = new ArrayCollection($rule['spp_levels']);
        foreach ($spps as $from => $level) {
            $to = $spps->next();
            if ($to) {
                for ($i = $from; $i < $spps->indexOf($to); $i++) {
                    if (!isset($spps[$i])) {
                        $spps[$i] = $level;
                    }
                }
            }
        }
        $spps = $spps->toArray();
        ksort($spps);
        $this->sppLevels = new ArrayCollection($spps);
    }

    private function prepareSkillsTable(string $ruleKey, array $rule)
    {
        $this->skills = new ArrayCollection();
        foreach ($rule['skills'] as $type => $skills) {
            foreach ($skills as $skill) {
                $key = join(CoreTranslation::TRANSLATION_GLUE, [$type, $skill]);
                $this->skills->set(
                    $key,
                    new Skill([
                        'key'         => $skill,
                        'type'        => $type,
                        'name'        => CoreTranslation::getSkillNameKey($ruleKey, $skill),
                        'description' => CoreTranslation::getSkillDescription($ruleKey, $skill),
                        'type_name'   => CoreTranslation::getSkillType($ruleKey, $type),
                        'translation_domain'      => $ruleKey,
                    ])
                );
            }
        }
    }

    private function prepareInducementTable(string $ruleKey, array $rule)
    {
        $this->inducements = new ArrayCollection();

        foreach ($rule['inducements'] as $key => $value) {
            if ($key !== 'star_players') {
                $inducement = new Inducement([
                    'type' => $this->inducementTypes['inducements'],
                    'key' => join(CoreTranslation::TRANSLATION_GLUE, [$ruleKey, 'inducements', $key]),
                    'translation_domain' => $ruleKey,
                    'name' => CoreTranslation::getInducementName($ruleKey, $key),
                    'max' => $value['max'] ?? 0,
                    'rosters' => $value['rosters'] ?? null,
                    'value' => $value['cost'],
                    'discount_value' => $value['discount_cost'] ?? null,
                ]);
                if (!$this->inducements->contains($inducement)) {
                    $this->inducements->add($inducement);
                }
            }
        }
        foreach ($rule['star_players'] as $key => $starPlayer) {
            $inducement = $this->createStarPlayerInducement($ruleKey, $key, $starPlayer);
            if (!$this->inducements->contains($inducement)) {
                $this->inducements->add($inducement);
            }
        }
    }

    private function prepareRosterTable(string $ruleKey, array $rule)
    {
        $this->rosters = new ArrayCollection();
        foreach ($rule['rosters'] as $key => $roster) {
            $this->rosters->set(
                $key,
                new Roster([
                    'key' => $key,
                    'name' => CoreTranslation::getRosterKey($ruleKey, $key),
                    'translation_domain' => $ruleKey,
                    'player_types' => $roster['players'],
                    'reroll_cost' => $roster['reroll_cost'] ?? 0,
                    'can_have_apothecary' => $roster['options']['can_have_apothecary'] ?? true,
                    'inducement_options' => $roster['options']['inducements'] ?? [],
                ])
            );
        }
    }

    private function createStarPlayerInducement(string $ruleKey, string $key, array $starPlayer):InducementInterface
    {
        $options = [
            'type' => $this->inducementTypes['star_players'],
            'key' => join(CoreTranslation::TRANSLATION_GLUE, [$ruleKey, 'star_players', $key]),
            'value' => $starPlayer['cost'],
            'discount_value' => $starPlayer['discount_cost'] ?? null,
            'translation_domain' => $ruleKey,
            'name' => CoreTranslation::getStarPlayerName($ruleKey, $key),
            'max' => $starPlayer['max'] ?? 1,
            'characteristics' => $starPlayer['characteristics'] ?? null,
            'skills' => $starPlayer['skills'] ?? null,
            'rosters' => $starPlayer['rosters'] ?? null,
        ];
        if (isset($starPlayer['multi_parts']) && $starPlayer['multi_parts']) {
            $options['parts'] = [];
            $first = true;
            foreach ($starPlayer['multi_parts'] as $key => $part) {
                $part['cost'] = $first ? $starPlayer['cost'] : 0;
                $options['parts'][] = $this->createStarPlayerInducement($ruleKey, $key, $part);
                $first = false;
            }
            $inducement = new MultipleStarPlayer($options);
        } else {
            $inducement = new StarPlayer($options);
        }
        return $inducement;
    }
}
