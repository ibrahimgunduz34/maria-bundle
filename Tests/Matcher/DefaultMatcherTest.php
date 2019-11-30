<?php
namespace SweetCode\MariaBundle\Tests\Matcher;

use PHPUnit\Framework\TestCase;
use SweetCode\MariaBundle\Matcher\DefaultMatcher;
use SweetCode\MariaBundle\Operator\Operator;

class DefaultMatcherTest extends TestCase
{
    /**
     * @param $return
     * @param $expect
     * @dataProvider dataProvider
     */
    public function testMatch($return, $expect)
    {
        $operator = $this->getMockBuilder(Operator::class)->disableOriginalConstructor()->getMock();

        $operator->method('compare')->willReturn($return);
        $matcher = new DefaultMatcher($operator);

        $this->assertEquals($expect, $matcher->match(['some_field' => 100]));
    }

    public function dataProvider()
    {
        return [
            [true, true],
            [false, false],
        ];
    }
}
