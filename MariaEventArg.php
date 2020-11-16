<?php


namespace SweetCode\MariaBundle;


use Symfony\Contracts\EventDispatcher\Event;

class MariaEventArg extends Event
{
    private $data;

    /**
     * MariaEventArg constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

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
