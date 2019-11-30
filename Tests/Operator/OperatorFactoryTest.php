<?php

namespace SweetCode\MariaBundle\Tests\Operator;

use PHPUnit\Framework\TestCase;
use SweetCode\MariaBundle\Operator\AndOperator;
use SweetCode\MariaBundle\Operator\BetweenOperator;
use SweetCode\MariaBundle\Operator\EqualOperator;
use SweetCode\MariaBundle\Operator\GreaterOperator;
use SweetCode\MariaBundle\Operator\GreaterOrEqualOperator;
use SweetCode\MariaBundle\Operator\InOperator;
use SweetCode\MariaBundle\Operator\LessOperator;
use SweetCode\MariaBundle\Operator\LessOrEqualOperator;
use SweetCode\MariaBundle\Operator\Operator;
use SweetCode\MariaBundle\Operator\OperatorFactory;
use SweetCode\MariaBundle\Operator\OrOperator;

class OperatorFactoryTest extends TestCase
{
    public function testCreateEql()
    {
        $rules = ['amount' => ['eql' => 100]];
        $factory = new OperatorFactory();

        /** @var Operator $operator */
        $operator = $factory->create($rules);
        $this->assertOperatorProperties(EqualOperator::class, $operator, 'amount', 100);
    }

    public function testCreateGt()
    {
        $rules = ['amount' => ['gt' => 100]];
        $factory = new OperatorFactory();
        /** @var Operator $operator */
        $operator = $factory->create($rules);
        $this->assertOperatorProperties(GreaterOperator::class, $operator, 'amount', 100);
    }

    public function testLessOperator()
    {
        $rules = ['amount' => ['lt' => 100]];
        $factory = new OperatorFactory();

        /** @var Operator $operator */
        $operator = $factory->create($rules);
        $this->assertOperatorProperties(LessOperator::class, $operator, 'amount', 100);
    }

    public function testCreateGte()
    {
        $rules = ['amount' => ['gte' => 100]];
        $factory = new OperatorFactory();
        /** @var Operator $operator */
        $operator = $factory->create($rules);
        $this->assertOperatorProperties(GreaterOrEqualOperator::class, $operator, 'amount', 100);
    }

    public function testCreateLte()
    {
        $rules = ['amount' => ['lte' => 100]];
        $factory = new OperatorFactory();

        /** @var Operator $operator */
        $operator = $factory->create($rules);
        $this->assertOperatorProperties(LessOrEqualOperator::class, $operator, 'amount', 100);
    }

    public function testCreateIn()
    {
        $rules = ['category_id' => ['lte' => [1,2,3]]];
        $factory = new OperatorFactory();

        /** @var Operator $operator */
        $operator = $factory->create($rules);
        $this->assertOperatorProperties(LessOrEqualOperator::class, $operator, 'category_id', [1,2,3]);
    }

    public function testCreateBtw()
    {
        $rules = ['amount' => ['btw' => [100, 200]]];
        $factory = new OperatorFactory();

        /** @var Operator $operator */
        $operator = $factory->create($rules);
        $this->assertOperatorProperties(BetweenOperator::class, $operator, 'amount', [100, 200]);
    }

    public function testCreateAnd()
    {
        $rules = [
            'amount' => ['eql' => 100],
            'category_id' => ['in' => [1,2,3]]
        ];
        $factory = new OperatorFactory();

        /** @var Operator $operator */
        $operator = $factory->create($rules);

        $expected = [
            EqualOperator::class => ['amount', 100],
            InOperator::class => ['category_id', [1, 2, 3]]
        ];

        $this->assertGroupOperator(AndOperator::class, $operator, $expected);
    }

    public function testCreateOr()
    {
        $rules = [
            ['amount' => ['eql' => 100]],
            ['category_id' => ['in' => [1,2,3]]]
        ];
        $factory = new OperatorFactory();

        /** @var Operator $operator */
        $operator = $factory->create($rules);

        $expected = [
            EqualOperator::class => ['amount', 100],
            InOperator::class => ['category_id', [1, 2, 3]]
        ];

        $this->assertGroupOperator(OrOperator::class, $operator, $expected);
    }

    public function testCreateNested()
    {
        $rules = [
            ['amount' => ['eql' => 100], 'category_id' => ['in' => [1,2,3]]],
            ['amount' => ['eql' => 200], 'category_id' => ['in' => [4, 5, 6]]],
        ];
        $factory = new OperatorFactory();

        /** @var Operator $operator */
        $operator = $factory->create($rules);

        $this->assertInstanceOf(OrOperator::class, $operator);
        $reflection = new \ReflectionObject($operator);
        $operatorsProp = $reflection->getProperty('operators');
        $operatorsProp->setAccessible(true);
        $operatorsPropVal = $operatorsProp->getValue($operator);
        $this->assertContainsOnlyInstancesOf(AndOperator::class, $operatorsPropVal);
    }

    /**
     * @param $class
     * @param Operator $operator
     * @param $field
     * @param $value
     * @throws \ReflectionException
     */
    private function assertOperatorProperties($class, Operator $operator, $field, $value)
    {
        $this->assertInstanceOf($class, $operator);;

        $reflection = new \ReflectionObject($operator);

        $fieldProp = $reflection->getProperty('field');
        $fieldProp->setAccessible(true);
        $this->assertEquals($field, $fieldProp->getValue($operator));

        $valueProp = $reflection->getProperty('value');
        $valueProp->setAccessible(true);
        $this->assertEquals($value, $valueProp->getValue($operator));
    }

    /**
     * @param $class
     * @param Operator $operator
     * @param array $expected
     * @throws \ReflectionException
     */
    private function assertGroupOperator($class, Operator $operator, array $expected)
    {
        $this->assertInstanceOf($class, $operator);
        $reflection = new \ReflectionObject($operator);
        $operatorsProp = $reflection->getProperty('operators');
        $operatorsProp->setAccessible(true);

        array_map(
            function ($operator, $class, $expectedValue) {
                list($field, $value) = $expectedValue;
                $this->assertOperatorProperties($class, $operator, $field, $value);
            },
            $operatorsProp->getValue($operator),
            array_keys($expected),
            array_values($expected)
        );
    }
}
