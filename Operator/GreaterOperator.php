<?php
namespace SweetCode\MariaBundle\Operator;

class GreaterOperator extends Operator
{
    protected function accept($data)
    {
        $field = $this->getField();
        if (!array_key_exists($field, $data)) {
            return false;
        }

        return $data[$field] > $this->getValue();
    }
}