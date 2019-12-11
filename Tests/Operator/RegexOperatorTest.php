<?php

namespace SweetCode\MariaBundle\Tests\Operator;

use PHPUnit\Framework\TestCase;
use SweetCode\MariaBundle\Operator\RegexOperator;

class RegexOperatorTest extends TestCase
{
    public function testCompareSuccess()
    {
        $operatorObject = new RegexOperator('field', "/gunduz/i");

        $data = ['field' => 'Ibrahim Gunduz is a developer'];
        $this->assertTrue($operatorObject->compare($data));
    }

    public function testCompareFail()
    {
        $operatorObject = new RegexOperator('field', "/manager/i");

        $data = ['field' => 'Ibrahim Gunduz is a developer'];
        $this->assertFalse($operatorObject->compare($data));
    }
}
