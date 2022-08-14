<?php

namespace Obblm\Core\Domain\Service\Rule\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Expression;
use Obblm\Core\Domain\Contracts\Rule\InducementInterface;
use Obblm\Core\Domain\Exception\NotFoundKeyException;
use Obblm\Core\Domain\Exception\UnexpectedTypeException;
use Obblm\Core\Domain\Model\Player;
use Obblm\Core\Domain\Model\PlayerVersion;
use Obblm\Core\Domain\Model\Proxy\Inducement\Inducement;
use Obblm\Core\Domain\Model\Proxy\Inducement\MultipleStarPlayer;
use Obblm\Core\Domain\Model\Proxy\Inducement\StarPlayer;
use Obblm\Core\Domain\Model\Team;
use Obblm\Core\Domain\Service\CoreTranslation;

/*********************
 * INDUCEMENT METHODS
 ********************/
trait InducementRuleTrait
{
    abstract public function getInducementTable(): ArrayCollection;

    private function getInducementExpression($options): CompositeExpression
    {
        $expressions = [];
        if (isset($options['type'])) {
            // Criteria by inducement type
            $expressions['type'] = $this->getTypeExpression($options);
        }
        if (isset($options['cost_limit'])) {
            // Criteria by cost limit
            $expressions['cost'] = $this->getBudgetExpression($options);
        }
        if (isset($options['roster'])) {
            // Criteria by roster
            $expressions['roster'] = $this->getRosterExpression($options);
        }

        return new CompositeExpression(CompositeExpression::TYPE_AND, $expressions);
    }

    private function getTypeExpression($options): Expression
    {
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

                return new CompositeExpression(CompositeExpression::TYPE_OR, $types);
            } elseif (is_string($options['type'])) {
                $inducementType = $this->getInducementType($options['type']);

                return Criteria::expr()->eq('type', $inducementType);
            }
            throw new \InvalidArgumentException('"type" option is not an array or a string');
        }
        throw new \InvalidArgumentException('"type" option must be defined');
    }

    private function getRosterExpression($options): Expression
    {
        if (isset($options['roster']) && is_string($options['roster'])) {
            // Criteria by roster
            return Criteria::expr()->orX(
                Criteria::expr()->memberOf('rosters', $options['roster']),
                Criteria::expr()->eq('rosters', [])
            );
        }
        throw new \InvalidArgumentException('"roster" option must be defined');
    }

    private function getBudgetExpression($options): Expression
    {
        if (isset($options['cost_limit']) && is_int($options['cost_limit'])) {
            // Criteria by cost limit
            return Criteria::expr()->lte('value', $options['cost_limit']);
        }
        throw new \InvalidArgumentException('"cost_limit" option must be defined');
    }

    public function getInducements(): ArrayCollection
    {
        $criteria = Criteria::create()
            ->where($this->getInducementExpression(['type' => 'inducements']));

        return $this->getInducementTable()->matching($criteria);
    }

    public function getStarPlayers(): ArrayCollection
    {
        $criteria = Criteria::create()
            ->where($this->getInducementExpression(['type' => 'star_players']));

        return $this->getInducementTable()->matching($criteria);
    }

    public function getInducementsFor(Team $team, ?int $budget = null, ?array $types = null): ArrayCollection
    {
        $criteria = Criteria::create();
        if (!$types) {
            $types = [
                'inducements',
                'star_players',
            ];
        }
        $expr['type'] = $types;
        $expr['roster'] = $team->getRoster();
        if (null !== $budget) {
            $expr['cost_limit'] = $budget;
        }
        $criteria->where(Criteria::expr()->andX(
            $this->getInducementExpression($expr)
        ));

        return $this->getInducementTable()->matching($criteria);
    }

    public function getInducementsByTeamOptions(array $options): array
    {
        $inducements = [];
        $ruleKey = $this->getKey();
        $availableInducements = $this->rule['inducements'];

        foreach ($availableInducements as $key => $value) {
            if ('star_players' !== $key) {
                if ($options[$key]) {
                    $inducement = [
                        'type' => $this->getInducementType('inducements'),
                        'key' => join(CoreTranslation::TRANSLATION_GLUE, [$ruleKey, 'inducements', $key]),
                        'translation_domain' => $this->getKey(),
                        'translation_key' => CoreTranslation::getInducementName($ruleKey, $key),
                        'max' => $value['max'] ?? 0,
                        'value' => ('discount' === $options[$key]) ? $value['discounted_cost'] : $value['cost'],
                    ];
                    $inducements[] = (new Inducement())->setOptions($inducement);
                }
            }
        }

        return $inducements;
    }

    public function getMaxStarPlayers(): int
    {
        return $this->rule['inducements']['star_players']['max'];
    }

    public function getStarPlayer(string $key): StarPlayer
    {
        $starPlayer = $this->getInducement($key);
        if (!$starPlayer instanceof StarPlayer) {
            throw new UnexpectedTypeException($starPlayer, StarPlayer::class);
        }

        return $starPlayer;
    }

    public function getInducement(string $key): InducementInterface
    {
        if (!$this->getInducementTable()->containsKey($key)) {
            throw new NotFoundKeyException($key, 'inducements', self::class);
        }

        return $this->getInducementTable()->get($key);
    }

    /**
     * @return array|InducementInterface[]
     */
    public function getAvailableStarPlayers(Team $team): array
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->andX(
            $this->getInducementExpression([
                'type' => 'star_players',
                'roster' => $team->getRoster(),
            ])
        ));

        return $this->getInducementTable()->matching($criteria)->toArray();
    }

    public function createInducementAsPlayer(InducementInterface $inducement, $number = 0): ?Player
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
            ->setPosition($inducement->getType()->getName())
            ->setName($inducement->getName())
            ->addVersion($version);

        return $player;
    }

    public function createStarPlayerAsPlayer(string $key, int $number, bool $hire = false): Player
    {
        $ruleKey = $this->getKey();

        $starPlayer = $this->getStarPlayer($key);
        if ($starPlayer instanceof StarPlayer) {
            if ($starPlayer instanceof MultipleStarPlayer) {
                throw new \Exception('You cannot create a player with a multiple parts InducementInterface');
            }
            $version = (new PlayerVersion())
                ->setCharacteristics($starPlayer->getCharacteristics())
                ->setValue($starPlayer->getValue())
                ->setHiredStarPlayer($hire);
            if ($starPlayer->getSkills()) {
                $version->setSkills($starPlayer->getSkills());
            }
            $player = (new Player())
                ->setNumber($number)
                ->setPosition($starPlayer->getKey())
                ->setName($starPlayer->getName())
                ->addVersion($version);

            return $player;
        }
    }

    public function getTransformedInducementsFor(string $roster)
    {
        $table = $this->getInducementTable();
        foreach ($table as $inducement) {
            if ('halfling' == $roster) {
                $inducement->setValue(10000);
            }
        }

        return $table;
    }
}
