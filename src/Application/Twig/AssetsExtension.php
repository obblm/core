<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Twig;

use Obblm\Core\Application\Service\AssetPackager;
use Obblm\Core\Domain\Helper\ImageHelper;
use Obblm\Core\Domain\Model\Team;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetsExtension extends AbstractExtension
{
    protected AssetPackager $packager;
    protected ImageHelper $imageHelper;

    public function __construct(AssetPackager $packager/*, ImageHelper $imageHelper*/)
    {
        $this->packager = $packager;
        //$this->imageHelper = $imageHelper;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('obblm_css', [$this, 'getObblmCss']),
            new TwigFunction('obblm_js', [$this, 'getObblmJs']),
            new TwigFunction('roster_image', [$this, 'getRosterImageUrl']),
            new TwigFunction('team_logo', [$this, 'getTeamLogo']),
            new TwigFunction('team_cover', [$this, 'getTeamCover']),
        ];
    }

    public function getObblmCss(string $entrypoint, $bundle = 'core'): array
    {
        return $this->packager->getCssEntry($entrypoint);
    }

    public function getObblmJs(string $entrypoint, $bundle = 'core'): array
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
