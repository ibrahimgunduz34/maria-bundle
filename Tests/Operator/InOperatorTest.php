<?php

namespace SweetCode\MariaBundle\Tests\Operator;

use PHPUnit\Framework\TestCase;
use SweetCode\MariaBundle\Operator\InOperator;

class InOperatorTest extends TestCase
{
    public function testCompareSuccess()
    {
        $operatorObject = new InOperator('category_id', [1,2,3]);

        $data = ['amount' => 100, 'category_id' => 1];
        $this->assertTrue($operatorObject->compare($data));

        $data = ['amount' => 100, 'category_id' => 3];
        $this->assertTrue($operatorObject->compare($data));
    }

    public function testCompareFail()
    {
        $operatorObject = new InOperator('category_id', [1,2,3]);

        $data = ['amount' => 150, 'category_id' => 20];
        $this->assertFalse($operatorObject->compare($data));
    }
}
