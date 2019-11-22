<?php
namespace SweetCode\MariaBundle\EventListener;

use SweetCode\MariaBundle\MariaEventArg;

interface MariaActionEventListener
{
    public function onAction(MariaEventArg $eventArg);
}