<?php
namespace SweetCode\MariaBundle\Operator;

class RegexOperator extends Operator
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

        return boolval(preg_match($this->getValue(), $data[$field]));
    }
}