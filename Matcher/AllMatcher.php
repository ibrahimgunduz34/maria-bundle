<?php
namespace SweetCode\MariaBundle\Matcher;

use SweetCode\MariaBundle\Exception\InvalidArgumentException;

class AllMatcher extends Matcher
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

        $result = null;
        foreach ($object as $item) {
            $compare = $this->getOperator()->compare($item);
            if ($result !== null && $result != $compare) {
                return false;
            }
            $result = $compare;
        }
        return $result;
    }
}