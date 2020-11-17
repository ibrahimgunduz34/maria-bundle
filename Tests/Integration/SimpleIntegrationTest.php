<?php
namespace SweetCode\MariaBundle\Tests\Integration;

use PHPUnit\Framework\TestCase;
use SweetCode\MariaBundle\DependencyInjection\MariaExtension;
use SweetCode\MariaBundle\MariaEventArg;
use SweetCode\MariaBundle\Tests\Stub\ValidActionHandler;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SimpleIntegrationTest extends TestCase
{
    /**
     * @param string $filename
     */
    private function getContainer($filename)
    {
        $container = new ContainerBuilder(new ParameterBag(['kernel.debug' => false]));
        $container->registerExtension(new MariaExtension());

        $locator = new FileLocator(__DIR__ . '/Fixtures');
        $loader = new YamlFileLoader($container, $locator);

        //Add event dispatcher
        $edDef = new Definition(EventDispatcher::class);
        $edDef->addTag('container.hot_path');
        $edDef->setPublic(true);
        $container->setDefinition('event_dispatcher', $edDef);

        $container->addCompilerPass(new RegisterListenersPass(), PassConfig::TYPE_BEFORE_REMOVING);

        $loader->load($filename);

        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->compile();

        return $container;
    }

    private function injectMockHandler($container, $serviceId, $mock)
    {
        //TODO: Find a better way to mock
        $trigger = $container->get($serviceId);
        $triggerRefl = new \ReflectionObject($trigger);
        $handlerProp = $triggerRefl->getProperty('actionHandler');
        $handlerProp->setAccessible(true);
        $handlerProp->setValue($trigger, $mock);
    }

    public function testScenarioMatching()
    {
        $mariaEventArg = new MariaEventArg(['amount' => 101]);

        //Just to keep backward compatibility for olrd php/phpunit version. To be removed.
        $container = $this->getContainer('simple-scenario.yaml');

        $this->mockActionHandler($mariaEventArg, $container, 'maria.trigger.some_scenario', $this->once());

        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $container->get('event_dispatcher');

        $dispatcher->dispatch('some.event', $mariaEventArg);
    }

    public function testUnmatchedScenario()
    {
        $mariaEventArg = new MariaEventArg(['amount' => 90]);

        //Just to keep backward compatibility for olrd php/phpunit version. To be removed.
        $container = $this->getContainer('simple-scenario.yaml');

        $this->mockActionHandler($mariaEventArg, $container, 'maria.trigger.some_scenario', $this->never());

        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $container->get('event_dispatcher');

        $dispatcher->dispatch('some.event', $mariaEventArg);
    }

    public function testConflictedScenarios()
    {
        $mariaEventArg = new MariaEventArg(['amount' => 300]);

        //Just to keep backward compatibility for olrd php/phpunit version. To be removed.
        $container = $this->getContainer('conflicted-scenarios.yaml');

        $this->mockActionHandler($mariaEventArg, $container, 'maria.trigger.some_scenario', $this->once());
        $this->mockActionHandler($mariaEventArg, $container, 'maria.trigger.other_scenario', $this->never());
        $this->mockActionHandler($mariaEventArg, $container, 'maria.trigger.first_scenario', $this->never());

        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $container->get('event_dispatcher');

        $dispatcher->dispatch('some.event', $mariaEventArg);
    }

    /**
     * @param MariaEventArg $mariaEventArg
     * @param ContainerBuilder $container
     */
    private function mockActionHandler(MariaEventArg $mariaEventArg, ContainerBuilder $container, $serviceId, $expects)
    {
        $mock = $this->getMockBuilder(ValidActionHandler::class)
            ->setMethods(['onAction'])
            ->getMock();

        $mock->expects($expects)
            ->method('onAction')
            ->with($mariaEventArg)
            ->willReturn(true);

        $this->injectMockHandler($container, $serviceId, $mock);
    }
}
