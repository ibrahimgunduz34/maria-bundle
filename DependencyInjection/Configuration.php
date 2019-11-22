<?php
namespace SweetCode\MariaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private $name;

    /**
     * Configuration constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder($this->name);
        if (method_exists(TreeBuilder::class, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root($this->name);
        }
        $this->buildScenarios($rootNode);
        return $treeBuilder;
    }

    private function buildScenarios(ArrayNodeDefinition $node)
    {
        $node->fixXmlConfig('scenario')
            ->useAttributeAsKey('name')
            ->arrayPrototype()
                ->canBeEnabled()
                ->children()
                    ->scalarNode('trigger_event')->isRequired()->end()
                    ->scalarNode('action_handler')->isRequired()->end()
                ->end()
                ->append($this->buildRules())
            ->end();
    }

    private function buildRules()
    {
        $treeBuilder = new TreeBuilder('rules');
        if (method_exists(TreeBuilder::class, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root($this->name);
        }

        $rootNode->fixXmlConfig('rule')
            ->useAttributeAsKey('object_type')
            ->arrayPrototype()
                ->variablePrototype()->end()
            ->end();
        //TODO: Validation need
        return $rootNode;
    }
}