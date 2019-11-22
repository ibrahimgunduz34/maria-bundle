<?php
namespace SweetCode\MariaBundle\DependencyInjection\CompilerPass;

use SweetCode\MariaBundle\Matcher\AnyMatcher;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class InjectComparators implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
//        $taggedMcServiceIds = $container->findTaggedServiceIds('maria.matcher_context');
//        foreach ($taggedMcServiceIds as $mcServiceId => $mcTags) {
//            $mcServiceDef = $container->getDefinition($mcServiceId);
//
//        }

    }
}