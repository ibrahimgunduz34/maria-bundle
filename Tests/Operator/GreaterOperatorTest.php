<?php

namespace SweetCode\MariaBundle\Tests\Operator;

use PHPUnit\Framework\TestCase;
use SweetCode\MariaBundle\Operator\GreaterOperator;

class GreaterOperatorTest extends TestCase
{
    public function testCompareSuccess()
    {
        $operatorObject = new GreaterOperator('amount', 50);
        
        $data = ['amount' => 100, 'category_id' => 1];
        $this->assertTrue($operatorObject->compare($data));
    }

    public function testCompareFail()
    {
        $operatorObject = new GreaterOperator('amount', 50);

        $data = ['amount' => 50, 'category_id' => 1];
        $this->assertFalse($operatorObject->compare($data));

        $data = ['amount' => 49, 'category_id' => 1];
        $this->assertFalse($operatorObject->compare($data));
    }
}
