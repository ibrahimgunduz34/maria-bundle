<?php
namespace SweetCode\MariaBundle\Tests\Operator;

use PHPUnit\Framework\TestCase;
use SweetCode\MariaBundle\Operator\EqualOperator;

class EqualOperatorTest extends TestCase
{
    public function testCompareSuccess()
    {
        $operatorObject = new EqualOperator('amount', 100);

        $data = ['amount' => 100, 'category_id' => 1];
        $this->assertTrue($operatorObject->compare($data));
    }

    public function testCompareFail()
    {
        $operatorObject = new EqualOperator('amount', 100);

        $data = ['amount' => 150, 'category_id' => 1];
        $this->assertFalse($operatorObject->compare($data));
    }
}
