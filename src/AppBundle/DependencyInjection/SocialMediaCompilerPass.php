<?php

namespace AppBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SocialMediaCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $contextDefinition = $container->findDefinition('app.social_media_context');

        $strategyServiceIds = array_keys(
            $container->findTaggedServiceIds('app.social_media')
        );

        foreach ($strategyServiceIds as $strategyServiceId) {
            $contextDefinition->addMethodCall(
                'addStrategy', [
                    new Reference($strategyServiceId)
                ]
            );
        }
    }
}