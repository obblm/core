<?php

namespace Obblm\Core\Domain\Service\Rule\Config;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Obblm\Core\Application\Service\CoreTranslation;
use Obblm\Core\Domain\Contracts\Rule\InducementInterface;
use Obblm\Core\Domain\Contracts\Rule\RosterInterface;
use Obblm\Core\Domain\Contracts\Rule\RuleBuilderInterface;
use Obblm\Core\Domain\Contracts\Rule\SkillInterface;
use Obblm\Core\Domain\Exception\NotFoundKeyException;
use Obblm\Core\Domain\Model\Proxy\Inducement\Inducement;
use Obblm\Core\Domain\Model\Proxy\Inducement\InducementType;
use Obblm\Core\Domain\Model\Proxy\Inducement\MultipleStarPlayer;
use Obblm\Core\Domain\Model\Proxy\Inducement\StarPlayer;
use Obblm\Core\Domain\Model\Proxy\Roster\Roster;
use Obblm\Core\Domain\Model\Proxy\Skill\Skill;
use Obblm\Core\Domain\Model\Team;
use phpDocumentor\Reflection\Types\Self_;

class RuleConfigBuilder extends RuleConfigResolver implements RuleBuilderInterface
{
    /** @var ArrayCollection */
    private $injuries = null;
    /** @var ArrayCollection|SkillInterface[] */
    private $skills = null;
    /** @var ArrayCollection|InducementType[] */
    private $inducementTypes = null;
    /** @var ArrayCollection */
    private $sppLevels = null;
    /** @var ArrayCollection|InducementInterface[] */
    private $inducements = null;
    /** @var ArrayCollection|RosterInterface[] */
    private $rosters = null;

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

    public function getInjuries(): ArrayCollection
    {
        return $this->injuries;
    }

    public function setInjuries(array $injuries): self
    {
        $this->injuries = $injuries;

        return $this;
    }

    public function getInducementTypes(): ArrayCollection
    {
        return $this->inducementTypes;
    }

    public function setInducementTypes(array $inducementTypes): self
    {
        $this->inducementTypes = $inducementTypes;

        return $this;
    }

    public function getInducementType(string $type): InducementType
    {
        return $this->getInducementTypes()->get($type);
    }

    public function getSppLevels(): ArrayCollection
    {
        return $this->sppLevels;
    }

    public function setSppLevels(array $sppLevels): self
    {
        $this->sppLevels = $sppLevels;

        return $this;
    }

    public function getInducementTable(): ArrayCollection
    {
        return $this->inducements;
    }

    public function setInducementTable(array $inducements): self
    {
        $this->inducements = $inducements;

        return $this;
    }

    /** @return RosterInterface[]|array */
    public function getRosters(): ArrayCollection
    {
        return $this->rosters;
    }

    public function getRoster(Team $team): RosterInterface
    {
        $roster = $this->rosters->get($team->getRoster());
        if (!$roster)
        {
            throw new NotFoundKeyException($team->getRoster(), 'rosters', self::class);
        }
        return $roster;
    }

    public function setRosters(array $rosters): self
    {
        $this->rosters = $rosters;

        return $this;
    }

    public function getSkills(): ArrayCollection
    {
        return $this->skills;
    }

    public function getSkill($key): SkillInterface
    {
        $criteria = (Criteria::create())
            ->where(
                Criteria::expr()
                    ->eq('key', $key)
            );
        $criteria->setMaxResults(1);
        $result = $this->getSkills()->matching($criteria);
        if (!$result->first()) {
            throw new NotFoundKeyException($key, 'skills', self::class);
        }

        return $result->first();
    }

    public function setSkills(array $skills): self
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
                for ($i = $injury['from']; $i <= $injury['to']; ++$i) {
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
        $this->inducementTypes->set('star_players', (new InducementType())->setOptions([
            'key' => 'star_players',
            'name' => CoreTranslation::getStarPlayerTitle($ruleKey),
            'translation_domain' => $ruleKey,
        ]));
        $this->inducementTypes->set('inducements', (new InducementType())->setOptions([
            'key' => 'inducements',
            'name' => CoreTranslation::getInducementTitle($ruleKey),
            'translation_domain' => $ruleKey,
        ]));
        $this->inducementTypes->set('mercenary', (new InducementType())->setOptions([
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
                for ($i = $from; $i < $spps->indexOf($to); ++$i) {
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
                    (new Skill())->setOptions([
                        'key' => $skill,
                        'type' => $type,
                        'name' => CoreTranslation::getSkillNameKey($ruleKey, $skill),
                        'description' => CoreTranslation::getSkillDescription($ruleKey, $skill),
                        'type_name' => CoreTranslation::getSkillType($ruleKey, $type),
                        'translation_domain' => $ruleKey,
                    ])
                );
            }
        }
    }

    private function prepareInducementTable(string $ruleKey, array $rule)
    {
        $this->inducements = new ArrayCollection();

        foreach ($rule['inducements'] as $key => $value) {
            if ('star_players' !== $key) {
                $inducement = (new Inducement())->setOptions([
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
                    $this->inducements->set($inducement->getKey(), $inducement);
                }
            }
        }
        foreach ($rule['star_players'] as $key => $starPlayer) {
            $inducement = $this->createStarPlayerInducement($ruleKey, $key, $starPlayer);
            if (!$this->inducements->contains($inducement)) {
                $this->inducements->set($inducement->getKey(), $inducement);
            }
        }
    }

    private function prepareRosterTable(string $ruleKey, array $rule)
    {
        $this->rosters = new ArrayCollection();
        foreach ($rule['rosters'] as $key => $roster) {
            $this->rosters->set(
                $key,
                (new Roster())->setOptions([
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

    private function createStarPlayerInducement(string $ruleKey, string $key, array $starPlayer): InducementInterface
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
            $inducement = (new MultipleStarPlayer())->setOptions($options);
        } else {
            $inducement = (new StarPlayer())->setOptions($options);
        }

        return $inducement;
    }

    public function getWeatherChoices(): array
    {
        $weather = [];
        $ruleKey = $this->getKey();
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
