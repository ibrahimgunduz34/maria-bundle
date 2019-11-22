<?php

namespace SweetCode\MariaBundle\Tests\Matcher;

use PHPUnit\Framework\TestCase;
use SweetCode\MariaBundle\Matcher\AnyMatcher;
use SweetCode\MariaBundle\Matcher\FirstMatcher;
use SweetCode\MariaBundle\Operator\Operator;

class FirstMatcherTest extends TestCase
{
    /**
     * @param $data
     * @param $valueMap
     * @param $expected
     * @dataProvider dataProvider
     */
    public function testMatch($data, $valueMap, $expected)
    {
        $operatorStub = $this->getMockBuilder(Operator::class)->disableOriginalConstructor()->getMock();
        $operatorStub->method('compare')->will($this->returnValueMap($valueMap));

        $matcher = new FirstMatcher($operatorStub);
        $this->assertEquals($expected, $matcher->match($data));
    }

    public function dataProvider()
    {
        $data = [
            ['amount' => 100, 'category_id'=>1],
            ['amount' => 20, 'category_id'=>1]
        ];

        return [
            [
                $data,
                [
                    [$data[0], true],
                    [$data[1], false]
                ],
                true
            ],
            [
                $data,
                [
                    [$data[0], false],
                    [$data[1], true]
                ],
                false
            ],
            [
                $data,
                [
                    [$data[0], false],
                    [$data[1], false]
                ],
                false
            ]
        ];
    }
}
