<?php
namespace SweetCode\MariaBundle\Comparator;

class InComparator extends Comparator
{
    protected function accept($object)
    {
        $field = $this->getField();
        $data = $this->getObjectNromalizer()->normalize($object);
        if (!array_key_exists($field, $data)) {
            return false;
        }
        return in_array($data[$field], $this->getValue());
    }
}