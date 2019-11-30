<?php
namespace SweetCode\MariaBundle\Operator;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class OperatorFactory
{
    /**
     * @param $rules
     * @return Operator
     */
    public function create($rules)
    {
        $isAssoc = (array() == $rules) ?
            false:
            array_keys($rules) !== range(0, count($rules)-1);
        if ($isAssoc) {
            return $this->createAndGroup($rules);
        } else {
            return $this->createOrGroup($rules);
        }
    }

    /**
     * @param $field
     * @param $condition
     * @return EqualOperator|GreaterOperator|GreaterOrEqualOperator|InOperator|LessOperator|LessOrEqualOperator|BetweenOperator
     */
    private function createComparisionOperator($field, $condition)
    {
        //condition must be consisted of one element.
        if (count($condition) > 1) {
            throw new InvalidConfigurationException(sprintf('Invalid rule definition %s => %s', $field, $condition));
        }
        $operatorTypes = array_keys($condition);
        $operatorType = reset($operatorTypes);
        $value = $condition[$operatorType];

        switch ($operatorType) {
            case 'gt':
                return new GreaterOperator($field, $value);
            case 'lt':
                return new LessOperator($field, $value);
            case 'gte':
                return new GreaterOrEqualOperator($field, $value);
            case 'lte':
                return new LessOrEqualOperator($field, $value);
            case 'eql':
                return new EqualOperator($field, $value);
            case 'in':
                return new InOperator($field, $value);
            case 'btw':
                return new BetweenOperator($field, $value);
            default:
                throw new InvalidConfigurationException('Invalid operator type: ' . $operatorType);
        }
    }

    /**
     * @param $rules
     * @return array|mixed|AndOperator
     */
    private function createAndGroup($rules)
    {
        $operators = [];
        foreach ($rules as $field => $condition) {
            $operator = $this->createComparisionOperator($field, $condition);
            $operators[] = $operator;
        }
        if (count($operators) > 1) {
            return new AndOperator($operators);
        } else {
            return reset($operators);
        }
    }

    /**
     * @param $rules
     * @return OrOperator
     */
    private function createOrGroup($rules): OrOperator
    {
        $operators = [];
        foreach ($rules as $rule) {
            $operators[] = $this->create($rule);
        }
        return new OrOperator($operators);
    }
}