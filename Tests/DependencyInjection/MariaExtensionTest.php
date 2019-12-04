<?php
namespace SweetCode\MariaBundle\Tests;

use PHPUnit\Framework\TestCase;
use SweetCode\MariaBundle\DependencyInjection\MariaExtension;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class MariaExtensionTest extends TestCase
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
        $loader->load($filename);
        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->compile();

        return $container;
    }

    public function testTriggerAndActionEvents()
    {
        $container = $this->getContainer('trigger_and_action_events.yaml');

        $triggerEventServiceId = 'maria.trigger.test_scenario';
        $this->assertTrue($container->has($triggerEventServiceId));
        $triggerEvent = $container->getDefinition($triggerEventServiceId);
        $this->assertEquals('%maria.event_listener.trigger.class%', $triggerEvent->getClass());
        $this->assertTrue($triggerEvent->hasTag('kernel.event_listener'));
        $tag = $triggerEvent->getTag('kernel.event_listener');
        $this->assertEquals(
            [
                'event' => 'test.event',
                'method' => 'onTrigger'
            ],
            reset($tag)
        );

        $actionHandlerId = 'maria.handler.test_scenario.SweetCode\MariaBundle\Tests\Stub\ValidActionHandler';
        $this->assertTrue($container->has($actionHandlerId));
        $this->assertTrue($container->has('maria.matcher_context.test_scenario'));
    }

    public function testActionEventImplementationValidation()
    {
        try {
            $this->getContainer('bad_action_event_implementation.yaml');
        } catch (\Exception $exception) {
            $this->assertInstanceOf(InvalidConfigurationException::class, $exception);
            $this->assertEquals('Handler class needs to implement onAction method', $exception->getMessage());
        }

    }
}