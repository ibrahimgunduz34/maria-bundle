<?php

namespace SweetCode\MariaBundle\Tests\Operator;

use PHPUnit\Framework\TestCase;
use SweetCode\MariaBundle\Operator\GreaterOrEqualOperator;

class GreaterOrEqualOperatorTest extends TestCase
{
    public function testCompareSuccess()
    {
        $operatorObject = new GreaterOrEqualOperator('amount', 100);

        $data = ['amount' => 100, 'category_id' => 1];
        $this->assertTrue($operatorObject->compare($data));

        $data = ['amount' => 101, 'category_id' => 1];
        $this->assertTrue($operatorObject->compare($data));
    }

    public function testCompareFail()
    {
        $operatorObject = new GreaterOrEqualOperator('amount', 100);

        $data = ['amount' => 99, 'category_id' => 1];
        $this->assertFalse($operatorObject->compare($data));
    }
}
