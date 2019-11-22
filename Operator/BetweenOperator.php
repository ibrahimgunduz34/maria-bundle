<?php
namespace SweetCode\MariaBundle\Operator;


class BetweenOperator extends Operator
{
    /**
     * @param array $data
     * @return bool
     */
    protected function accept($data)
    {
        $field = $this->getField();
        if (!array_key_exists($field, $data)) {
            return false;
        }
        $value = $this->getValue();
        return $data[$field] >= $value[0] && $data[$field] <= $value[1];
    }
}