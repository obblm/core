<?php

namespace Obblm\Core\Helper\Rule;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Obblm\Core\Contracts\InducementInterface;
use Obblm\Core\Contracts\RosterInterface;
use Obblm\Core\Contracts\Rule\RuleBuilderInterface;
use Obblm\Core\Entity\Team;
use Obblm\Core\Exception\NotFoundRuleKeyExcepion;
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
    private $inducement_types;
    /** @var ArrayCollection */
    private $spp_levels;
    /** @var ArrayCollection|InducementInterface[] */
    private $inducements;
    /** @var ArrayCollection|RosterInterface[] */
    private $rosters;

    protected function build(string $rule_key, array $rule) {

        $treeResolver = new ConfigResolver($this);
        $rule = $treeResolver->resolve($rule);
        $this->prepareInjuriesTable($rule_key, $rule);
        $this->prepareSppTable($rule_key, $rule);
        $this->prepareSkillsTable($rule_key, $rule);
        $this->prepareInducementTypes($rule_key, $rule);
        $this->prepareInducementTable($rule_key, $rule);
        $this->prepareRosterTable($rule_key, $rule);
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
        return $this->inducement_types;
    }

    public function setInducementTypes(ArrayCollection $inducement_types):self
    {
        $this->inducement_types = $inducement_types;
        return $this;
    }

    public function getSppLevels():ArrayCollection
    {
        return $this->spp_levels;
    }

    public function setSppLevels(ArrayCollection $spp_levels):self
    {
        $this->spp_levels = $spp_levels;
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

    public function getRosters():ArrayCollection
    {
        return $this->rosters;
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

    private function prepareInjuriesTable(string $rule_key, array $rule)
    {
        $this->injuries = new ArrayCollection();
        foreach ($rule['injuries'] as $key => $injury) {
            $label = CoreTranslation::getInjuryKey($rule_key, $key);
            $effect_label = CoreTranslation::getInjuryEffect($rule_key, $key);
            if (isset($injury['to'])) {
                for ($i = $injury['from']; $i <= $injury['to']; $i++) {
                    $this->injuries->set(
                        $i,
                        (object) ['value' => $i, 'label' => $label, 'effect_label' => $effect_label, 'effects' => $injury['effects']]
                    );
                }
            } else {
                $this->injuries->set(
                    $injury['from'],
                    (object) ['value' => $injury['from'], 'label' => $label, 'effect_label' => $effect_label, 'effects' => $injury['effects']]
                );
            }
        }
    }

    private function prepareInducementTypes(string $rule_key, array $rule)
    {
        $this->inducement_types = new ArrayCollection();
        $this->inducement_types->set('star_players', new InducementType([
            'key' => 'star_players',
            'translation_key' => CoreTranslation::getStarPlayerTitle($rule_key),
            'translation_domain' => $rule_key,
        ]));
        $this->inducement_types->set('inducements', new InducementType([
            'key' => 'inducements',
            'translation_key' => CoreTranslation::getInducementTitle($rule_key),
            'translation_domain' => $rule_key,
        ]));
        $this->inducement_types->set('mercenary', new InducementType([
            'key' => 'mercenary',
            'translation_key' => CoreTranslation::getMercenaryTitle($rule_key),
            'translation_domain' => $rule_key,
        ]));
    }

    private function prepareSppTable(string $rule_key, array $rule)
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
        $this->spp_levels = new ArrayCollection($spps);
    }

    private function prepareSkillsTable(string $rule_key, array $rule)
    {
        $this->skills = new ArrayCollection();
        foreach ($rule['skills'] as $type => $skills) {
            foreach ($skills as $skill) {
                $key = join(CoreTranslation::TRANSLATION_GLUE, [$type, $skill]);
                $this->skills->set(
                    $key,
                    new Skill([
                        'key' => $skill,
                        'type' => $type,
                        'translation_key' => CoreTranslation::getSkillNameKey($rule_key, $skill),
                        'type_translation_key' => CoreTranslation::getSkillType($rule_key, $type),
                        'translation_domain' => $rule_key,
                    ])
                );
            }
        }
    }

    private function prepareInducementTable(string $rule_key, array $rule)
    {
        $this->inducements = new ArrayCollection();

        foreach ($rule['inducements'] as $key => $value) {
            if ($key !== 'star_players') {
                $inducement = new Inducement([
                    'type' => $this->inducement_types['inducements'],
                    'key' => join(CoreTranslation::TRANSLATION_GLUE, [$rule_key, 'inducements', $key]),
                    'translation_domain' => $rule_key,
                    'translation_key' => CoreTranslation::getInducementName($rule_key, $key),
                    'max' => $value['max'] ?? 0,
                    'rosters' => $value['rosters'] ?? null,
                    'value' => $value['cost'],
                    'discount_cost' => $value['discount_cost'] ?? null,
                ]);
                if (!$this->inducements->contains($inducement)) {
                    $this->inducements->add($inducement);
                }
            }
        }
        foreach ($rule['star_players'] as $key => $star_player) {
            try {
                $inducement = $this->createStarPlayerInducement($rule_key, $key, $star_player);
                if (!$this->inducements->contains($inducement)) {
                    $this->inducements->add($inducement);
                }
            } catch (NotFoundRuleKeyExcepion $e) {
            }
        }
    }

    private function prepareRosterTable(string $rule_key, array $rule)
    {
        $this->rosters = new ArrayCollection();
        foreach ($rule['rosters'] as $key => $roster) {
            $this->rosters->set(
                $key,
                new Roster([
                    'key' => $key,
                    'translation_key' => CoreTranslation::getRosterKey($rule_key, $key),
                    'translation_domain' => $rule_key,
                    'player_types' => $roster['players'],
                    'reroll_cost' => $roster['reroll_cost'] ?? 0,
                    'can_have_apothecary' => $roster['options']['can_have_apothecary'] ?? true,
                    'inducement_options' => $roster['options']['inducements'] ?? [],
                ])
            );
        }
    }

    private function createStarPlayerInducement(string $rule_key, string $key, array $star_player):InducementInterface
    {
        $options = [
            'type' => $this->inducement_types['star_players'],
            'key' => join(CoreTranslation::TRANSLATION_GLUE, [$rule_key, 'star_players', $key]),
            'value' => $star_player['cost'],
            'discount_cost' => $value['discount_cost'] ?? null,
            'characteristics' => $star_player['characteristics'] ?? null,
            'skills' => $star_player['skills'] ?? null,
            'rosters' => $star_player['rosters'] ?? null,
            'translation_domain' => $rule_key,
            'translation_key' => CoreTranslation::getStarPlayerName($rule_key, $key),
            'max' => $options['max'] ?? 1,
        ];
        if (isset($star_player['multi_parts']) && $star_player['multi_parts']) {
            $options['parts'] = [];
            $first = true;
            foreach ($star_player['multi_parts'] as $key => $part) {
                $part['cost'] = $first ? $star_player['cost'] : 0;
                $options['parts'][] = $this->createStarPlayerInducement($rule_key, $key, $part);
                $first = false;
            }
            $inducement = new MultipleStarPlayer($options);
        } else {
            $inducement = new StarPlayer($options);
        }
        return $inducement;
    }

    protected function getInducementExpression($options = ['type' => 'inducements']):CompositeExpression
    {
        $sub_expressions = [];
        if (isset($options['type'])) {
            // Criteria by inducement type
            if (is_array($options['type'])) {
                $types = [];
                foreach ($options['type'] as $type) {
                    if (is_string($type)) {
                        $inducementType = $this->getInducementType($type);
                        $types[] = Criteria::expr()->eq('type', $inducementType);
                    }
                }
                $sub_expressions['type'] = new CompositeExpression(CompositeExpression::TYPE_OR, $types);
                ;
            } elseif (is_string($options['type'])) {
                $inducementType = $this->getInducementType($options['type']);
                $sub_expressions['type'] = Criteria::expr()->eq('type', $inducementType);
            }
        }
        if (isset($options['cost_limit'])) {
            // Criteria by cost limit
            if (is_int($options['cost_limit'])) {
                $sub_expressions['cost'] = Criteria::expr()->lte('value', $options['cost_limit']);
            }
        }
        if (isset($options['roster'])) {
            // Criteria by roster
            if (is_string($options['roster'])) {
                $sub_expressions['roster'] = Criteria::expr()->orX(
                    Criteria::expr()->memberOf('rosters', $options['roster']),
                    Criteria::expr()->eq('rosters', [])
                );
            }
        }
        $composite = new CompositeExpression(CompositeExpression::TYPE_AND, $sub_expressions);
        return $composite;
    }

    public function getInducementsFor(Team $team, ?int $budget = null):array
    {
        $criteria = Criteria::create();
        $expr = [
            'type' => [
                'inducements',
                'star_players'
            ],
            'roster' => $team->getRoster()
        ];
        if ($budget !== null) {
            $expr['cost_limit'] = $budget;
        }
        $criteria->where(Criteria::expr()->andX(
            $this->getInducementExpression($expr)
        ));
        $available_inducements = $this->getInducements()->matching($criteria);
        return $available_inducements->toArray();
    }

    public function getAllStarPlayers():array
    {
        $criteria = Criteria::create();

        $criteria->where(Criteria::expr()->andX(
            $this->getInducementExpression([
                'type' => 'star_players'
            ])
        ));
        return $this->getInducementTable()->matching($criteria)->toArray();
    }
}
