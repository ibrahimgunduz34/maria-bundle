<?php
namespace SweetCode\MariaBundle\Listener;

use SweetCode\MariaBundle\MariaEventArg;
use SweetCode\MariaBundle\Matcher\Matcher;

class TriggerEventListener
{
    /** @var Matcher */
    private $matcher;

    private $actionHandler;

    private $handlerMethod;

    private $serialize;

    /**
     * TriggerEventListener constructor.
     * @param Matcher $matcher
     * @param $actionHandler
     * @param $handlerMethod
     * @param $serialize
     */
    public function __construct(Matcher $matcher, $actionHandler, $handlerMethod, $serialize)
    {
        $this->matcher = $matcher;
        $this->actionHandler = $actionHandler;
        $this->handlerMethod = $handlerMethod;
        $this->serialize = $serialize;
    }


    public function onTrigger(MariaEventArg $event)
    {
        if ( $this->matcher->match($event->getData()) ) {
            call_user_func(
                [$this->actionHandler, $this->handlerMethod],
                $this->serialize ? serialize($event) : $event
            );

            $event->stopPropagation();
        }
    }
}