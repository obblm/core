<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Service;

use Obblm\Core\Application\Exception\NotFoundEntrypointException;
use Obblm\Core\Domain\Contracts\BuildAssetsInterface;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

class AssetPackager
{
    private Package $package;
    private $entrypoints = [];

    public function addBuildAsset(BuildAssetsInterface $buildAssets)
    {
        $this->load($buildAssets->getPath());
    }

    public function addDirectory($directory)
    {
        $this->load($directory);
    }

    private function load($directory)
    {
        $manifestPath = $directory.'/manifest.json';
        $entrypointsPath = $directory.'/entrypoints.json';

        $this->package = new Package(new JsonManifestVersionStrategy($manifestPath));

        if (!is_file($entrypointsPath)) {
            throw new \RuntimeException(sprintf('Entrypoints file "%s" does not exist.', $entrypointsPath));
        }
        $json = file_get_contents($entrypointsPath);

        $loadedEntrypoints = json_decode($json, true);

        $this->entrypoints = array_merge_recursive($this->entrypoints, $loadedEntrypoints['entrypoints']);

        return $this->package;
    }

    public function getCssEntry(string $entrypoint)
    {
        $entrypoint = $this->getEntryPoint($entrypoint);

        return isset($entrypoint['css']) ? $entrypoint['css'] : [];
    }

    public function getJsEntry(string $entrypoint)
    {
        $entrypoint = $this->getEntryPoint($entrypoint);

        return isset($entrypoint['js']) ? $entrypoint['js'] : [];
    }

    public function getEntryPoint(string $entrypoint)
    {
        if (!isset($this->entrypoints[$entrypoint])) {
            throw new NotFoundEntrypointException($entrypoint, self::class);
        }

        return $this->entrypoints[$entrypoint];
    }
}
