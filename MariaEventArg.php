<?php


namespace SweetCode\MariaBundle;


use Symfony\Component\EventDispatcher\Event;

class MariaEventArg extends Event
{
    private $data;

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}