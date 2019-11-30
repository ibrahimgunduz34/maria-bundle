<?php
namespace SweetCode\MariaBundle\Matcher;

use SweetCode\MariaBundle\Operator\Operator;

abstract class Matcher
{
    /** @var Operator */
    protected $operator;

    public function __construct(Operator $operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return Operator
     */
    protected function getOperator()
    {
        return $this->operator;
    }

    abstract public function match($object);
}