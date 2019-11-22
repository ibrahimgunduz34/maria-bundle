<?php
namespace SweetCode\MariaBundle\DependencyInjection;

use SweetCode\MariaBundle\Comparator\EqualComparator;
use SweetCode\MariaBundle\EventListener\MariaActionEventListener;
use SweetCode\MariaBundle\Matcher\Matcher;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class MariaExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(array(__DIR__. '/../Resources/config')));
        $loader->load("matchers.yaml");
        $loader->load("comparators.yaml");
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
            $this->createActionHandler($key, $scenario, $container);
            $mcDefinition = $this->createMatcherContext($key, $scenario, $container);
            $trigger->addArgument($mcDefinition);
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
        $triggerServiceId = sprintf('maria.trigger_event.%s', $name);
        $definition = new Definition('%maria.event_listener.trigger_event.class%');
        $definition->addTag('kernel.event_listener', ['event' => $scenario['trigger_event'], 'method' => 'onTrigger']);
        $container->setDefinition($triggerServiceId, $definition);
        return $definition;
    }

    /**
     * @param $scenario
     * @param ContainerBuilder $container
     */
    private function createActionHandler($name, $scenario, ContainerBuilder $container)
    {
        if ($container->hasDefinition($scenario['action_handler'])) {
            $ref = new Reference($scenario['action_handler']);
        } else {
            $ref = $scenario['action_handler'];
        }

        $definition = new Definition($ref);

        if (!is_subclass_of($definition->getClass(), MariaActionEventListener::class)) {
            throw new InvalidConfigurationException(
                'Action handler needs to be implementation of ' . MariaActionEventListener::class
            );
        }
        $serviceId = sprintf('maria.action_handler.%s.%s', $name, $scenario['action_handler']);
        $definition->addTag('kernel.event_listener', ['event' => $serviceId, 'method' => 'onTrigger']);
        $container->setDefinition($serviceId, $definition);
    }

    /**
     * @param $key
     * @param $scenario
     * @param ContainerBuilder $container
     * @return Definition
     */
    private function createMatcherContext($key, $scenario, ContainerBuilder $container)
    {
        $mcServiceId = sprintf('maria.matcher_context.%s', $key);
        $mcDefinition = new Definition(Matcher::class);
        $mcDefinition->setFactory([new Reference('maria.service_factory.matcher_context_factory'), 'create']);
        $mcDefinition->setArguments([$scenario['rules']]);
        $mcDefinition->addTag('maria.matcher_context');
        $container->setDefinition($mcServiceId, $mcDefinition);
        return $mcDefinition;
    }


}