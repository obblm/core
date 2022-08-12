<?php

namespace Obblm\Core\Domain\Service\Rule;

use Obblm\Core\Domain\Contracts\Rule\PositionInterface;
use Obblm\Core\Domain\Contracts\RuleHelperInterface;
use Obblm\Core\Domain\Model\PlayerVersion;

class Bb2020Rule extends AbstractRuleHelper implements RuleHelperInterface
{
    public function getActionsFormClass(): string
    {
        return ''; //ActionBb2020Type::class;
    }

    public function getKey(): string
    {
        return 'bb2020';
    }

    public function setPlayerDefaultValues(PlayerVersion $version, PositionInterface $position): ?PlayerVersion
    {
        $version->setCharacteristics($position->getCharacteristics())
            ->setActions([
                'td' => 0,
                'cas' => 0,
                'pas' => 0,
                'int' => 0,
                'mvp' => 0,
            ])
            ->setSkills($position->getSkills())
            ->setValue($position->getCost())
            ->setSppLevel($this->getSppLevel($version));

        return $version;
    }
}
