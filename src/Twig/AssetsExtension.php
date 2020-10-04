<?php

namespace Obblm\Core\Twig;

use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\Team;
use Obblm\Core\Helper\AssetPackager;
use Obblm\Core\Helper\ImageHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetsExtension extends AbstractExtension
{
    protected $imageHelper;
    protected $packager;

    public function __construct(AssetPackager $packager, ImageHelper $imageHelper)
    {
        $this->packager = $packager;
        $this->imageHelper = $imageHelper;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('roster_image', [$this, 'getRosterImageUrl']),
            new TwigFunction('team_logo', [$this, 'getTeamLogo']),
            new TwigFunction('team_cover', [$this, 'getTeamCover']),
            new TwigFunction('obblm_css', [$this, 'getObblmCss']),
            new TwigFunction('obblm_js', [$this, 'getObblmJs']),
        ];
    }

    public function getObblmCss(string $entrypoint, $bundle = 'core')
    {
        return $this->packager->getCssEntry($entrypoint);
    }

    public function getObblmJs(string $entrypoint, $bundle = 'core')
    {
        return $this->packager->getJsEntry($entrypoint);
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
