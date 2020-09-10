<?php

namespace Obblm\Core\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension {

    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']),
            new TwigFilter('yesno', [$this, 'formatBooleanToString']),
            new TwigFilter('generate_steps', [$this, 'formatSteps']),
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
        return number_format($number, $decimals, $decPoint, $thousandsSep);
    }
    public function formatBooleanToString(bool $var):string {
        return ($var) ? 'yes' : 'no';
    }
    public function formatSteps(array $steps, $context):array {
        krsort($steps);
        $nextDone = false;
        $nextCurrent = true;
        foreach($steps as $key => $step) {
            $step['is_current'] = (isset($context[$step['id']]) && $context[$step['id']]) ? $nextCurrent : false;
            $step['is_done'] = $nextDone ? true : false;
            if($step['is_current']) {
                $nextDone = true;
                $nextCurrent = false;
            }
            $steps[$key] = $step;
        }
        ksort($steps);
        return $steps;
    }
}