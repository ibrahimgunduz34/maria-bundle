<?php

namespace SweetCode\MariaBundle\Tests\Operator;

use PHPUnit\Framework\TestCase;
use SweetCode\MariaBundle\Operator\AndOperator;
use SweetCode\MariaBundle\Operator\Operator;
use SweetCode\MariaBundle\Operator\OrOperator;
use SweetCode\MariaBundle\Tests\Stub\DataObject;

class OrOperatorTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     * @param $c1
     * @param $c2
     * @param $expected
     */
    public function testCompare($data, $valueMap1, $valueMap2, $expected)
    {
        $operator1 = $this->createOperatorMock($valueMap1);
        $operator2 = $this->createOperatorMock($valueMap2);

        $operator = new OrOperator([$operator1, $operator2]);
        $this->assertEquals($expected, $operator->compare($data));
    }

    private function createOperatorMock($valueMap)
    {
        $stub = $this->getMockBuilder(Operator::class)->disableOriginalConstructor()->getMock();
        $stub->method('compare')->will($this->returnValueMap($valueMap));
        return $stub;
    }

    public function dataProvider()
    {
        $data = ['amount' => 100, 'category_id' => 1];
        return [
            [
                $data,
                [[$data, true]],
                [[$data, true]],
                true
            ],
            [
                $data,
                [[$data, true]],
                [[$data, false]],
                true
            ],
            [
                $data,
                [[$data, false]],
                [[$data, false]],
                false
            ],
        ];
    }
}
