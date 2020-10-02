<?php

namespace Obblm\Core\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']),
            new TwigFilter('yesno', [$this, 'formatBooleanToString']),
        ];
    }
    public function getFunctions()
    {
        return [
            new TwigFunction('area', [$this, 'calculateArea']),
        ];
    }

    public function calculateArea(int $width, int $length)
    {
        return $width * $length;
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
        return ($var) ? 'yes' : 'no';
    }
}
