<?php
namespace SweetCode\MariaBundle\Operator;

class AndOperator extends Operator
{
    /**
     * @var Operator[]
     */
    private $operators;

    /**
     * OrOperator constructor.
     * @param Operator[] $operators
     */
    public function __construct(array $operators)
    {
        parent::__construct(null, null);
        $this->operators = $operators;
    }

    public function compare($input)
    {
        $result = null;
        foreach ($this->operators as $operator) {
            $compare = $operator->compare($input);
            if ($result !== null && $result != $compare) {
                return false;
            }
            $result = $compare;
        }
        return $result;
    }

    /**
     * @param array $data
     * @return bool
     */
    protected function accept($data)
    {
        return true;
    }
}