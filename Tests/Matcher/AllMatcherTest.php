<?php

namespace SweetCode\MariaBundle\Tests\Matcher;

use PHPUnit\Framework\TestCase;
use SweetCode\MariaBundle\Matcher\AllMatcher;
use SweetCode\MariaBundle\Operator\Operator;

class AllMatcherTest extends TestCase
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

        $matcher = new AllMatcher($operatorStub);
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
                false
            ],
            [
                $data,
                [
                    [$data[0], true],
                    [$data[1], true]
                ],
                true
            ]
        ];
    }
}
