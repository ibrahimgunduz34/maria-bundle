<?php
namespace SweetCode\MariaBundle\Comparator;

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

abstract class Comparator
{
    private $objectNromalizer;

    private $field;

    private $value;

    private $nextComparator;

    /**
     * @return mixed
     */
    protected function getField()
    {
        return $this->field;
    }

    /**
     * @return mixed
     */
    protected function getValue()
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    protected function getNextComparator()
    {
        return $this->nextComparator;
    }

    /**
     * Comparator constructor.
     * @param $field
     * @param $value
     * @param $nextComparator
     */
    public function __construct($field, $value, Comparator $nextComparator=null)
    {
        $this->field = $field;
        $this->value = $value;
        $this->nextComparator = $nextComparator;
        $this->objectNromalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
    }

    /**
     * @return ObjectNormalizer
     */
    protected function getObjectNromalizer()
    {
        return $this->objectNromalizer;
    }

    public function compare($object) {
        return ($this->getNextComparator() !== null) ? $this->getNextComparator()->compare($object) : true &&
            $this->accept($object);
    }

    abstract protected function accept($object);
}