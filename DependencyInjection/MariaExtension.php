<?php
namespace SweetCode\MariaBundle\DependencyInjection;

use SweetCode\MariaBundle\Matcher\Matcher;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;


class MariaExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(array(__DIR__ . '/../Resources/config')));
        $loader->load("maria.yaml");

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        $this->loadScenarios($config, $container);
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($this->getAlias());
    }

    private function loadScenarios(array $config, ContainerBuilder $container)
    {
        foreach ($config as $key => $scenario) {
            $trigger = $this->createTriggerEvent($key, $scenario, $container);
            $actionHandler = $this->createActionHandler($key, $scenario, $container);
            $mcDefinition = $this->createMatcher($key, $scenario, $container);
            $trigger->setArguments([$mcDefinition, $actionHandler, $scenario['handler']['method'], $scenario['handler']['serialize']]);
        }
    }

    /**
     * @param $name
     * @param $scenario
     * @param ContainerBuilder $container
     * @return Definition
     */
    private function createTriggerEvent($name, $scenario, ContainerBuilder $container)
    {
        $triggerServiceId = sprintf('maria.trigger.%s', $name);
        $definition = new Definition('%maria.event_listener.trigger.class%');
        $definition->addTag('kernel.event_listener', ['event' => $scenario['trigger'], 'method' => 'onTrigger']);
        $definition->setPublic(true); //For test
        $container->setDefinition($triggerServiceId, $definition);
        return $definition;
    }

    /**
     * @param $scenario
     * @param ContainerBuilder $container
     */
    private function createActionHandler($name, $scenario, ContainerBuilder $container)
    {
        $handler = $scenario['handler'];
        if ($container->hasDefinition($handler['reference'])) {
            $ref = new Reference($handler['reference']);
        } elseif (!class_exists($handler['reference'])) {
            throw new InvalidConfigurationException(sprintf('not existing action handler: %s', $handler['reference']));
        } else {
            $ref = $handler['reference'];
        }

        $definition = new Definition($ref);
        $definition->setPublic(true); //For tests.


        if (!method_exists($definition->getClass(), $handler['method'])) {
            throw new InvalidConfigurationException(
                sprintf('Handler class needs to implement %s method', $handler['method'])
            );
        }

        $serviceId = sprintf('maria.handler.%s.%s', $name, $handler['reference']);
//        $definition->addTag('kernel.event_listener', ['event' => $serviceId, 'method' => 'onTrigger']);
        $container->setDefinition($serviceId, $definition);
        return $definition;
    }

    /**
     * @param $key
     * @param $scenario
     * @param ContainerBuilder $container
     * @return Definition
     */
    private function createMatcher($key, $scenario, ContainerBuilder $container)
    {
        $mcServiceId = sprintf('maria.matcher_context.%s', $key);
        $mcDefinition = new Definition(Matcher::class);
        $mcDefinition->setFactory([new Reference('maria.service_factory.matcher'), 'create']);
        $mcDefinition->setArguments([$scenario['rules']]);
        $mcDefinition->addTag('maria.matcher_context');
        $container->setDefinition($mcServiceId, $mcDefinition);
        return $mcDefinition;
    }


}