<?php
namespace SweetCode\MariaBundle\Operator;

class EqualOperator extends Operator
{
    protected function accept($data)
    {
        //TODO: Refactor
        $field = $this->getField();
        if (!array_key_exists($field, $data)) {
            return false;
        }
        return $data[$field] == $this->getValue();
    }
}