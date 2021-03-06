<?php
namespace SweetCode\MariaBundle\Matcher;

use SweetCode\MariaBundle\Exception\InvalidArgumentException;

class FirstMatcher extends Matcher
{
    public function match($object)
    {
        if (!is_iterable($object)) {
            throw new InvalidArgumentException('Input argument must be iterable.');
        }

        $first = reset($object);
        return $this->getOperator()->compare($first);
    }
}