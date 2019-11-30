<?php
namespace SweetCode\MariaBundle\Operator;

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

abstract class Operator
{
    protected $objectNormalizer;

    protected $field;

    protected $value;
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
     * Comparator constructor.
     * @param $field
     * @param $value
     * @param $nextComparator
     */
    public function __construct($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
        $this->objectNormalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
    }

    /**
     * @return ObjectNormalizer
     */
    protected function getObjectNormalizer()
    {
        return $this->objectNormalizer;
    }

    public function compare($input) {
        if (is_object($input)) {
            $data = $this->getObjectNormalizer()->normalize($input);
        } else {
            $data = $input;
        }
;
        return $this->accept($data);
    }

    /**
     * @param array $data
     * @return bool
     */
    abstract protected function accept($data);
}