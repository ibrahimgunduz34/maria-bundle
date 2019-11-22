<?php

namespace SweetCode\MariaBundle\Tests\Listener;

use PHPUnit\Framework\TestCase;
use SweetCode\MariaBundle\Listener\TriggerEventListener;
use SweetCode\MariaBundle\MariaEventArg;
use SweetCode\MariaBundle\Matcher\Matcher;

class TriggerEventListenerTest extends TestCase
{
    public function testTriggerNoMatch()
    {
        $matcherMock = $this->getMockBuilder(Matcher::class)->disableOriginalConstructor()->getMock();
        $matcherMock->method('match')->willReturn(false);

        $actionHandlerMock = $this->getMockBuilder(\stdClass::class)
            ->setMockClassName('MockActionHandler')
            ->setMethods(['onAction'])
            ->getMock();

        $actionHandlerMock->expects($this->never())
            ->method('onAction');

        $eventMock = $this->getMockBuilder(MariaEventArg::class)->disableOriginalConstructor()->getMock();
        $eventMock->expects($this->never())
            ->method('stopPropagation');

        $listener = new TriggerEventListener($matcherMock, $actionHandlerMock, 'onAction', false);
        $listener->onTrigger($eventMock);
    }

    public function testTriggerMatch()
    {
        $matcherMock = $this->getMockBuilder(Matcher::class)->disableOriginalConstructor()->getMock();
        $matcherMock->method('match')->willReturn(true);

        $actionHandlerMock = $this->getMockBuilder(\stdClass::class)
            ->setMockClassName('MockActionHandler')
            ->setMethods(['onAction'])
            ->getMock();
        $actionHandlerMock->expects($this->once())
            ->method('onAction');

        $eventMock = $this->getMockBuilder(MariaEventArg::class)->disableOriginalConstructor()->getMock();
        $eventMock->expects($this->once())
            ->method('stopPropagation');

        $listener = new TriggerEventListener($matcherMock, $actionHandlerMock, 'onAction', false);
        $listener->onTrigger($eventMock);
    }
}
