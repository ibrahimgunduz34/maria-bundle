<?php
namespace SweetCode\MariaBundle\DependencyInjection;

use SweetCode\MariaBundle\Comparator\Comparator;
use SweetCode\MariaBundle\Comparator\EqualComparator;
use SweetCode\MariaBundle\Comparator\GreaterComparator;
use SweetCode\MariaBundle\Comparator\GreaterOrEqualComparator;
use SweetCode\MariaBundle\Comparator\InComparator;
use SweetCode\MariaBundle\Comparator\LessComparator;
use SweetCode\MariaBundle\Comparator\LessOrEqualComparator;
use SweetCode\MariaBundle\Matcher\AllMatcher;
use SweetCode\MariaBundle\Matcher\AnyMatcher;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;

class MatcherContextFactory
{
//    private $comparators;
//
//    public function addComparator($name, Comparator $comparator)
//    {
//        $this->comparators[$name] = $comparator;
//    }

//    )

    public function create($scenarioRules)
    {
        $objectType = key($scenarioRules);
        $ruleSet = $scenarioRules[$objectType];
        foreach ($ruleSet as $rules) {
            $comparator = null;
            foreach ($rules as $key => $value) {
                $splitKey = explode('__', $key);
                //TODO: Move the validation to the configuration tree building phase.
                if (count($splitKey) > 2) {
                    throw new InvalidConfigurationException(
                        'An object rule must be consist of a field name and a condition'
                    );
                } else if (count($splitKey) < 2) {
                    //TODO: Move the abbreviations to constants.
                    $splitKey[] = 'eql';
                }
                list($field, $condition) = $splitKey;

                //TODO: Refactor | Use dependency injection or service factory
                switch ($condition) {
                    case 'eql':
                        $comparator = new EqualComparator($field, $value, $comparator);
                    case 'gt':
                        $comparator = new GreaterComparator($field, $value, $comparator);
                    case 'gte':
                        $comparator = new GreaterOrEqualComparator($field, $value, $comparator);
                    case 'lt':
                        $comparator = new LessComparator($field, $value, $comparator);
                    case 'lte':
                        $comparator = new LessOrEqualComparator($field, $value, $comparator);
                    case 'in':
                        $comparator = new InComparator($field, $value, $comparator);
                }
            }
        }
    }
    }
