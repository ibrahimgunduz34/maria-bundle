<?php
namespace SweetCode\MariaBundle\Matcher;

use SweetCode\MariaBundle\Comparator\Comparator;

class AllMatcher extends Matcher
{
    public function match($object)
    {
        foreach ($this->getComparators() as $comparator) {

        }
    }
}