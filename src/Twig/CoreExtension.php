<?php

namespace Obblm\Core\Twig;

use Obblm\Core\Event\TwigCollectorEvent;
use Obblm\Core\Helper\CoreTranslation;
use Obblm\Core\Helper\LocaleHelper;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Twig\Parts\NavigationCollection;
use Obblm\Core\Twig\Parts\NavigationElementInterface;
use Obblm\Core\Twig\Parts\NavigationLink;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CoreExtension extends AbstractExtension
{
    private $ruleHelper;
    private $dispatcher;
    private $localeHelper;
    private $router;

    public function __construct(RuleHelper $ruleHelper, EventDispatcherInterface $dispatcher, LocaleHelper $localeHelper, RouterInterface $router)
    {
        $this->ruleHelper = $ruleHelper;
        $this->dispatcher = $dispatcher;
        $this->localeHelper = $localeHelper;
        $this->router = $router;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('is_collection', [$this, 'isCollection']),
            new TwigFilter('is_link', [$this, 'isLink']),
            new TwigFilter('price', [$this, 'formatPrice']),
            new TwigFilter('yesno', [$this, 'formatBooleanToString']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_navigation_links', [$this, 'getNavigationLinks']),
            new TwigFunction('get_admin_links', [$this, 'getAdminLinks']),
            new TwigFunction('rules_navigation', [$this, 'getRulesNavigation']),
            new TwigFunction('available_rules', [$this, 'getAvailableRules']),
            new TwigFunction('get_available_locales', [$this, 'getAvailableLocales']),
        ];
    }

    public function getAvailableLocales($current = null)
    {
        return $this->localeHelper->getLocalizedRoutes();
    }

    public function getRulesNavigation()
    {
        $rulesCollection = new NavigationCollection('obblm.forms.team.create.select.rules', 'list');
        foreach ($this->getAvailableRules() as $rule) {
            $ruleName = CoreTranslation::getRuleTitle($rule->getRuleKey());
            $rulesCollection->addToCollection(
                (new NavigationLink('obblm_team_create_rule', $ruleName, ['rule' => $rule->getId()]))
                    ->setTranslationDomain($rule->getRuleKey())
            );
        }

        $rulesNavigation = (new NavigationCollection())
            ->addToCollection($rulesCollection);
        $collector = new TwigCollectorEvent($rulesNavigation);
        $this->dispatcher->dispatch($collector, TwigCollectorEvent::COLLECT_TEAM_CREATION_BAR);
        return $rulesNavigation->getCollection();
    }

    public function getAvailableRules()
    {
        return $this->ruleHelper->getRulesAvailableForTeamCreation();
    }

    public function getNavigationLinks():array
    {
        $collection = new NavigationCollection();
        $this->dispatcher->dispatch(new TwigCollectorEvent($collection), TwigCollectorEvent::COLLECT_NAV_BAR);
        return $collection->getCollection();
    }

    public function isCollection(NavigationElementInterface $item)
    {
        return $item instanceof NavigationCollection;
    }

    public function isLink(NavigationElementInterface $item)
    {
        return $item instanceof NavigationLink;
    }

    public function getAdminLinks():array
    {
        $collection = new NavigationCollection();
        $this->dispatcher->dispatch(new TwigCollectorEvent($collection), TwigCollectorEvent::COLLECT_ADMIN_BAR);
        return $collection->getCollection();
    }

    public function formatPrice($number, $decimals = 0, $decPoint = '.', $thousandsSep = ','):string
    {
        if ($number === '') {
            return '';
        }
        return number_format($number, $decimals, $decPoint, $thousandsSep);
    }

    public function formatBooleanToString(bool $var):string
    {
        return ($var) ? 'obblm.layout.tools.yes' : 'obblm.layout.tools.no';
    }
}
