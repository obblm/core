<?php

namespace Obblm\Core\Helper;

use Obblm\Core\Exception\NotFoundEntrypointException;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

class AssetPackager
{
    private $entrypoints = [];

    public function __construct()
    {
        $this->addDirectory(dirname(__DIR__) . '/Resources/public/build');
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

        $this->entrypoints = array_merge($this->entrypoints, $loadedEntrypoints['entrypoints']);
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
