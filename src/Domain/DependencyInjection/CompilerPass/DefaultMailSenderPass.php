<?php

namespace Obblm\Core\Domain\DependencyInjection\CompilerPass;

use Obblm\Core\Domain\Contracts\DefaultSenderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DefaultMailSenderPass implements CompilerPassInterface
{
    const SERVICE_TAG = 'obblm.default.mail';
    const SENDER_ADDRESS = 'obblm.default.mail.email';
    const SENDER_NAME = 'obblm.default.mail.sender';

    public function process(ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(DefaultSenderInterface::class)
            ->addTag(self::SERVICE_TAG)
        ;
        $email = $container->getParameter(self::SENDER_ADDRESS);
        $name = $container->getParameter(self::SENDER_NAME);
        foreach ($container->findTaggedServiceIds(self::SERVICE_TAG) as $id => $tags) {
            $definition = $container->findDefinition($id);
            $definition->addMethodCall('setDefaultSender', [$email, $name]);
        }
    }
}
