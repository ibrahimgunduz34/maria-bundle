<?php
namespace SweetCode\MariaBundle\Operator;

class InOperator extends Operator
{
    protected function accept($data)
    {
        $field = $this->getField();
        if (!array_key_exists($field, $data)) {
            return false;
        }
        return in_array($data[$field], $this->getValue());
    }
}