<?php
namespace SweetCode\MariaBundle\Matcher;

use SweetCode\MariaBundle\Comparator\Comparator;

abstract class Matcher
{
    /** @var Comparator[] */
    private $comparators;

    public function __construct(array $comparators)
    {
        $this->comparators = $comparators;
    }

    /**
     * @return Comparator[]
     */
    protected function getComparators()
    {
        return $this->comparators;
    }

    abstract public function match($object);
}