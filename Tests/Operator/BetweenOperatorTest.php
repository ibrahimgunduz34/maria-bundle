<?php
namespace SweetCode\MariaBundle\Tests\Operator;

use PHPUnit\Framework\TestCase;
use SweetCode\MariaBundle\Operator\BetweenOperator;

class BetweenOperatorTest extends TestCase
{
    public function testCompareSuccess()
    {
        $operatorObject = new BetweenOperator('amount', [100, 150]);

        $data = ['amount' => 100, 'category_id' => 1];
        $this->assertTrue($operatorObject->compare($data));
    }

    public function testCompareFail()
    {
        $operatorObject = new BetweenOperator('amount', [100, 150]);

        $data = ['amount' => 169, 'category_id' => 1];
        $this->assertFalse($operatorObject->compare($data));
    }
}
