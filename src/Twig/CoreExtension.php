<?php

namespace Obblm\Core\Twig;

use Obblm\Core\Event\RulesCollectorEvent;
use Obblm\Core\Event\TwigCollectorEvent;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Twig\Parts\NavigationCollection;
use Obblm\Core\Twig\Parts\NavigationElementInterface;
use Obblm\Core\Twig\Parts\NavigationLink;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CoreExtension extends AbstractExtension {

    private $ruleHelper;
    private $dispatcher;

    public function __construct(RuleHelper $ruleHelper, EventDispatcherInterface $dispatcher) {
        $this->ruleHelper = $ruleHelper;
        $this->dispatcher = $dispatcher;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('is_collection', [$this, 'isCollection']),
            new TwigFilter('is_link', [$this, 'isLink']),
        ];
    }
    public function getFunctions()
    {
        return [
            new TwigFunction('get_navigation_links', [$this, 'getNavigationLinks']),
            new TwigFunction('get_admin_links', [$this, 'getAdminLinks']),
            new TwigFunction('rules_navigation', [$this, 'getRulesNavigation']),
            new TwigFunction('available_rules', [$this, 'getAvailableRules']),
        ];
    }

    public function getRulesNavigation() {
        $rules_collection = new NavigationCollection('obblm.forms.team.create.select.rules', 'list');
        foreach($this->getAvailableRules() as $rule) {
            $rule_name = 'obblm.rules.' . $rule->getRuleKey() . '.title';
            $rules_collection->addToCollection(
                new NavigationLink('obblm_team_create_rule', $rule_name, ['rule' => $rule->getId()])
            );
        }

        $rules_navigation = (new NavigationCollection())
            ->addToCollection($rules_collection);
        $collector = new TwigCollectorEvent($rules_navigation);
        $this->dispatcher->dispatch($collector, TwigCollectorEvent::COLLECT_TEAM_CREATION_BAR);
        return $rules_navigation->getCollection();
    }

    public function getAvailableRules()
    {
        return $this->ruleHelper->getRulesAvailableForTeamCreation();
    }

    public function getNavigationLinks():array {
        $collection = new NavigationCollection();
        $this->dispatcher->dispatch(new TwigCollectorEvent($collection), TwigCollectorEvent::COLLECT_NAV_BAR);
        return $collection->getCollection();
    }

    public function isCollection(NavigationElementInterface $item) {
        return $item instanceof NavigationCollection;
    }

    public function isLink(NavigationElementInterface $item) {
        return $item instanceof NavigationLink;
    }

    public function getAdminLinks():array  {
        $collection = new NavigationCollection();
        $this->dispatcher->dispatch(new TwigCollectorEvent($collection), TwigCollectorEvent::COLLECT_ADMIN_BAR);
        return $collection->getCollection();
    }
}