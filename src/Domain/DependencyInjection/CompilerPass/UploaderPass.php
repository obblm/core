<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\DependencyInjection\CompilerPass;

use Obblm\Core\Domain\Helper\FileTeamUploader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class UploaderPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $teamUploaderDefinition = $container->getDefinition(FileTeamUploader::class);
        $teamUploaderDefinition->addMethodCall('setUploader', [new Reference('obblm.team.uploader')]);
    }
}
