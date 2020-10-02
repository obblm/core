<?php

namespace Obblm\Core\Twig;

use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\Team;
use Obblm\Core\Helper\ImageHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetsExtension extends AbstractExtension
{
    protected $imageHelper;

    public function __construct(ImageHelper $imageHelper)
    {
        $this->imageHelper = $imageHelper;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('roster_image', [$this, 'getRosterImageUrl']),
            new TwigFunction('team_logo', [$this, 'getTeamLogo']),
            new TwigFunction('team_cover', [$this, 'getTeamCover']),
        ];
    }

    public function getRosterImageUrl(Rule $rule, string $roster, int $width = null, int $height = 150)
    {
        return $this->imageHelper->getRosterImage($rule, $roster, $width, $height);
    }

    public function getTeamLogo(Team $team, int $width = 200, int $height = null)
    {
        return $this->imageHelper->getTeamLogo($team, $width, $height);
    }

    public function getTeamCover(Team $team, int $width = null, int $height = null)
    {
        return $this->imageHelper->getTeamCover($team, $width, $height);
    }
}
