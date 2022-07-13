<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Twig;

use Obblm\Core\Application\Service\LocaleHelper;
use Obblm\Core\Application\Twig\Parts\NavigationCollection;
use Obblm\Core\Application\Twig\Parts\NavigationElementInterface;
use Obblm\Core\Application\Twig\Parts\NavigationLink;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class LayoutExtension extends AbstractExtension
{
    private LocaleHelper $localeHelper;

    public function __construct(LocaleHelper $localeHelper)
    {
        $this->localeHelper = $localeHelper;
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
            new TwigFunction('get_available_locales', [$this, 'getAvailableLocales']),
            new TwigFunction('get_navigation_links', [$this, 'getNavigationLinks']),
            new TwigFunction('get_admin_links', [$this, 'getAdminLinks']),
        ];
    }

    public function isCollection(NavigationElementInterface $item)
    {
        return $item instanceof NavigationCollection;
    }

    public function isLink(NavigationElementInterface $item)
    {
        return $item instanceof NavigationLink;
    }

    public function formatPrice($number, $decimals = 0, $decPoint = '.', $thousandsSep = ','): string
    {
        if ('' === $number) {
            return '';
        }

        return number_format($number, $decimals, $decPoint, $thousandsSep);
    }

    public function formatBooleanToString(bool $var): string
    {
        return ($var) ? 'obblm.layout.tools.yes' : 'obblm.layout.tools.no';
    }

    public function getAvailableLocales($current = null)
    {
        return $this->localeHelper->getLocalizedRoutes();
    }

    public function getNavigationLinks(): array
    {
        $collection = new NavigationCollection();
        //$this->dispatcher->dispatch(new TwigCollectorEvent($collection), TwigCollectorEvent::COLLECT_NAV_BAR);
        return $collection->getCollection();
    }

    public function getAdminLinks(): array
    {
        $collection = new NavigationCollection();
        //$this->dispatcher->dispatch(new TwigCollectorEvent($collection), TwigCollectorEvent::COLLECT_ADMIN_BAR);
        return $collection->getCollection();
    }
}
