<?php
namespace SweetCode\MariaBundle\Matcher;

use SweetCode\MariaBundle\Exception\InvalidArgumentException;

class AnyMatcher extends Matcher
{
    public function match($object)
    {
        /**
         * @TODO In fact, all and any operators are totally the same with AND and OR operators.
         * For the next version the project might need a refactoring in order to reuse operators
         * to perform matching operation.
         */

        if (!is_iterable($object)) {
            throw new InvalidArgumentException('Input argument must be iterable.');
        }

        foreach ($object as $item) {
            if ($this->getOperator()->compare($item)) {
                return true;
            }
        }
        return false;
    }
}