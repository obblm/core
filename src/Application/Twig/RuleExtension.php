<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Twig;

use Obblm\Core\Domain\Model\Rule;
use Obblm\Core\Domain\Service\CoreTranslation;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class RuleExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('rule_name', [$this, 'getRuleName']),
        ];
    }

    public function getRuleName(Rule $rule)
    {
        return CoreTranslation::getRuleTitle($rule->getRuleKey());
    }
}
