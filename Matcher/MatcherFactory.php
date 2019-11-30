<?php
namespace SweetCode\MariaBundle\Matcher;

use SweetCode\MariaBundle\Operator\OperatorFactory;

class MatcherFactory
{
    const MATCHER_ALL = 'all';
    const MATCHER_ANY = 'any';
    const MATCHER_NONE = 'none';
    const MATCHER_FIRST = 'first';
    const MATCHER_LAST = 'last';
    /** @var OperatorFactory */
    private $operatorFactory;

    /**
     * MatcherContextFactory constructor.
     * @param OperatorFactory $operatorFactory
     */
    public function __construct(OperatorFactory $operatorFactory)
    {
        $this->operatorFactory = $operatorFactory;
    }

    public function create($scenarioRules)
    {

        if(count($scenarioRules) !== 1) {
            $scenarioRules = ['default' => $scenarioRules];
        } else {
            $matchers = [
                self::MATCHER_ALL,
                self::MATCHER_ANY,
                self::MATCHER_NONE,
                self::MATCHER_FIRST,
                self::MATCHER_LAST
            ];

            $matchersInScenario = array_keys($scenarioRules);
            if (!in_array(strtolower(reset($matchersInScenario)), $matchers)) {
                $scenarioRules = ['default' => $scenarioRules];
            }
        }

        return $this->createMatcher($scenarioRules);
    }

    /**
     * @param $scenarioRules
     * @return AllMatcher|AnyMatcher|DefaultMatcher|FirstMatcher|LastMatcher|NoneMatcher
     */
    private function createMatcher($scenarioRules)
    {
        $rules = reset($scenarioRules);
        $keys = array_keys($scenarioRules);
        $matcherType = reset($keys);
        $operator = $this->operatorFactory->create($rules);

        switch ($matcherType) {
            case self::MATCHER_ANY:
                return new AnyMatcher($operator);
            case self::MATCHER_ALL:
                return new AllMatcher($operator);
            case self::MATCHER_NONE:
                return new NoneMatcher($operator);
            case self::MATCHER_FIRST:
                return new FirstMatcher($operator);
            case self::MATCHER_LAST:
                return new LastMatcher($operator);
            case 'default':
                return new DefaultMatcher($operator);
        }
    }
}