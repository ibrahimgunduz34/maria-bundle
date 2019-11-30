<?php
namespace SweetCode\MariaBundle\Matcher;

use SweetCode\MariaBundle\Exception\InvalidArgumentException;

class NoneMatcher extends Matcher
{
    public function match($object)
    {
        if (!is_iterable($object)) {
            throw new InvalidArgumentException('Input argument must be iterable.');
        }
        
        foreach ($object as $item) {
            if ($this->getOperator()->compare($item)) {
                return false;
            }
        }
        return true;
    }
}