<?php

namespace SweetCode\MariaBundle\Tests\Matcher;

use PHPUnit\Framework\TestCase;
use ReflectionObject;
use SweetCode\MariaBundle\Matcher\AnyMatcher;
use SweetCode\MariaBundle\Matcher\DefaultMatcher;
use SweetCode\MariaBundle\Matcher\MatcherFactory;
use SweetCode\MariaBundle\Operator\AndOperator;
use SweetCode\MariaBundle\Operator\GreaterOperator;
use SweetCode\MariaBundle\Operator\InOperator;
use SweetCode\MariaBundle\Operator\LessOperator;
use SweetCode\MariaBundle\Operator\OperatorFactory;
use SweetCode\MariaBundle\Operator\OrOperator;

class MatcherFactoryTest extends TestCase
{

    public function testAndOperatorWithDefaultMatcher()
    {
        $ruleSet = [
            'amount'        => ['gt' => 100],
            'category_id'   => ['in' => [1,2,3]]
        ];

        $mcFactory = new MatcherFactory(new OperatorFactory());
        $matcher = $mcFactory->create($ruleSet);
        $this->assertInstanceOf(DefaultMatcher::class, $matcher);

        $matcherReflection = new ReflectionObject($matcher);
        $operatorPRop = $matcherReflection->getProperty('operator');
        $operatorPRop->setAccessible(true);
        $matcherInternalOperator = $operatorPRop->getValue($matcher);
        $this->assertGroupOpt(AndOperator::class, $matcherInternalOperator, [
            function($operator) {
                $this->assertOperator(GreaterOperator::class, $operator, 'amount', 100);
            },
            function($operator) {
                $this->assertOperator(InOperator::class, $operator, 'category_id', [1, 2, 3]);
            }
        ]);
    }

    public function testOrOperatorWithDefaultMatcher()
    {
        $ruleSet = [
            ['amount'        => ['gt' => 100]],
            ['amount'        => ['lt' => 100]]
        ];

        $mcFactory = new MatcherFactory(new OperatorFactory());
        $matcher = $mcFactory->create($ruleSet);
        $this->assertInstanceOf(DefaultMatcher::class, $matcher);

        $matcherReflection = new ReflectionObject($matcher);
        $operatorPRop = $matcherReflection->getProperty('operator');
        $operatorPRop->setAccessible(true);
        $matcherInternalOperator = $operatorPRop->getValue($matcher);
        $this->assertGroupOpt(OrOperator::class, $matcherInternalOperator, [
            function($operator) {
                $this->assertOperator(GreaterOperator::class, $operator, 'amount', 100);
            },
            function($operator) {
                $this->assertOperator(LessOperator::class, $operator, 'amount', 100);
            },
        ]);
    }

    public function testOrAndMixedOperatorsWithDefaultMatcher()
    {
        $ruleSet = [
            [
                'amount'        => ['gt' => 100],
                'brand_id'   => ['in' => [1,3,5]]
            ],
            [
                'amount'        => ['gt' => 200],
                'category_id'   => ['in' => [4,6,8]]
            ]
        ];

        $mcFactory = new MatcherFactory(new OperatorFactory());
        $matcher = $mcFactory->create($ruleSet);
        $this->assertInstanceOf(DefaultMatcher::class, $matcher);

        $matcherReflection = new ReflectionObject($matcher);
        $operatorPRop = $matcherReflection->getProperty('operator');
        $operatorPRop->setAccessible(true);
        $matcherInternalOperator = $operatorPRop->getValue($matcher);
        $this->assertGroupOpt(OrOperator::class, $matcherInternalOperator, [
            function($orInternalOperator) {
                $this->assertGroupOpt(AndOperator::class, $orInternalOperator, [
                    function($operator) {
                        $this->assertOperator(GreaterOperator::class, $operator, 'amount', 100);
                    },
                    function($operator) {
                        $this->assertOperator(InOperator::class, $operator, 'brand_id', [1,3,5]);
                    },
                ]);
            },
            function($orInternalOperator) {
                $this->assertGroupOpt(AndOperator::class, $orInternalOperator, [
                    function($operator) {
                        $this->assertOperator(GreaterOperator::class, $operator, 'amount', 200);
                    },
                    function($operator) {
                        $this->assertOperator(InOperator::class, $operator, 'category_id', [4, 6, 8]);
                    },
                ]);
            },
        ]);
    }

    public function testAndOperatorWithSpecificMatcher()
    {
        $ruleSet = [
            'any' => [
                'amount'        => ['gt' => 100],
                'category_id'   => ['in' => [1,2,3]]
            ]
        ];

        $mcFactory = new MatcherFactory(new OperatorFactory());
        $matcher = $mcFactory->create($ruleSet);
        $this->assertInstanceOf(AnyMatcher::class, $matcher);

        $matcherReflection = new ReflectionObject($matcher);
        $operatorPRop = $matcherReflection->getProperty('operator');
        $operatorPRop->setAccessible(true);
        $matcherInternalOperator = $operatorPRop->getValue($matcher);
        $this->assertGroupOpt(AndOperator::class, $matcherInternalOperator, [
            function($operator) {
                $this->assertOperator(GreaterOperator::class, $operator, 'amount', 100);
            },
            function($operator) {
                $this->assertOperator(InOperator::class, $operator, 'category_id', [1, 2, 3]);
            }
        ]);
    }

    /**
     * @param $class
     * @param $operator
     * @param $field
     * @param $value
     * @throws \ReflectionException
     */
    private function assertOperator($class, $operator, $field, $value)
    {
        $this->assertInstanceOf($class, $operator);

        $gtOpRefl = new ReflectionObject($operator);

        $gtOpFieldProp = $gtOpRefl->getProperty('field');
        $gtOpFieldProp->setAccessible(true);
        $this->assertEquals($field, $gtOpFieldProp->getValue($operator));

        $gtOptValueProp = $gtOpRefl->getProperty('value');
        $gtOptValueProp->setAccessible(true);
        $this->assertEquals($value, $gtOptValueProp->getValue($operator));
    }

    /**
     * @param $inOperator
     * @param $field
     * @param $value
     * @throws \ReflectionException
     */
    private function assertInOpt($inOperator, $field, $value)
    {
        $this->assertInstanceOf(InOperator::class, $inOperator);

        $inOptRefl = new ReflectionObject($inOperator);

        $inOptFieldProp = $inOptRefl->getProperty('field');
        $inOptFieldProp->setAccessible(true);;
        $this->assertEquals($field, $inOptFieldProp->getValue());

        $inOptValueProp = $inOptRefl->getProperty('value');
        $inOptValueProp->setAccessible(true);
        $this->assertEquals($value, $inOptValueProp->getValue());
    }

    /**
     * @param $class
     * @param $baseOperator
     * @param $subAssertions
     * @throws \ReflectionException
     */
    private function assertGroupOpt($class, $baseOperator, $subAssertions)
    {
        $this->assertInstanceOf($class, $baseOperator);
        $andOperatorReflection = new ReflectionObject($baseOperator);
        $andOperatorInternalOpsProp = $andOperatorReflection->getProperty('operators');
        $andOperatorInternalOpsProp->setAccessible(true);;
        $andOperatorInternalOps = $andOperatorInternalOpsProp->getValue($baseOperator);
        $this->assertCount(count($subAssertions), $andOperatorInternalOps);
        array_map(function($operator, $assertion) {
            $assertion($operator);
        }, $andOperatorInternalOps, $subAssertions);
    }
}
