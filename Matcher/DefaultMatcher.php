<?php
namespace SweetCode\MariaBundle\Matcher;

class DefaultMatcher extends Matcher
{
    public function match($object)
    {
        return $this->getOperator()->compare($object);
    }
}