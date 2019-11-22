<?php
namespace SweetCode\MariaBundle\Operator;

class OrOperator extends Operator
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
        foreach ($this->operators as $operator) {
            if ($operator->compare($input)) {
                return true;
            }
        }
        return false;
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