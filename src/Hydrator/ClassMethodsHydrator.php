<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Hydrator;

use Hydrator\Context\ExtractionContext;
use Hydrator\Context\HydrationContext;
use Hydrator\Filter\CompositeFilter;
use Hydrator\Filter\GetFilter;
use Hydrator\Filter\HasFilter;
use Hydrator\Filter\IsFilter;
use Hydrator\Filter\OptionalParametersFilter;

/**
 * This hydrator uses getter/setter methods to extract/hydrate, respectively
 *
 * To keep this hydrator as efficient as possible, it makes some assumptions about your
 * code and your conventions. For instance, it will only check get/is/has methods for
 * extraction, and set methods for hydration. It also assumes that object properties
 * are camelCased, which is PSR-1 convention.
 *
 * If you have very specific use cases, you are encouraged to create your own hydrator
 */
class ClassMethodsHydrator extends AbstractHydrator
{
    /**
     * Holds the methods for a given object
     *
     * @var array
     */
    private $objectMethodsCache = [];

    /**
     * Holds the names of the methods used for hydration, indexed by class::property name,
     * false if the hydration method is not callable/usable for hydration purposes
     *
     * @var string[]|bool[]
     */
    private $hydrationMethodsCache = [];

    /**
     * A map of extraction methods to property name to be used during extraction, indexed
     * by class name and method name
     *
     * @var array
     */
    private $extractionMethodsCache = [];

    /**
     * Constructor
     *
     * @param array $strategies
     */
    public function __construct(array $strategies = [])
    {
        parent::__construct($strategies);

        $filter1 = new CompositeFilter([new GetFilter(), new HasFilter(), new IsFilter()]);
        $filter2 = new CompositeFilter([new OptionalParametersFilter()]);

        $this->compositeFilter = new CompositeFilter([$filter1, $filter2], CompositeFilter::CONDITION_AND);
    }

    /**
     * {@inheritDoc}
     */
    public function extract($object)
    {
        $objectClass = get_class($object);

        if (isset($this->objectMethodsCache[$objectClass])) {
            $methods = $this->objectMethodsCache[$objectClass];
        } else {
            $methods = $this->objectMethodsCache[$objectClass] = get_class_methods($object);
        }

        $result  = [];
        $context = new ExtractionContext($object);

        // Pass 1: finding out which properties can be extracted, with which methods (populate hydration cache)
        if (!isset($this->extractionMethodsCache[$objectClass])) {
            $this->extractionMethodsCache[$objectClass] = [];

            foreach ($methods as $method) {
                if (!$this->compositeFilter->accept($method, $context)) {
                    continue;
                }

                $property = preg_replace('/get/', '', $method); // Allow to strip "get" for getters
                $property = $this->namingStrategy->getNameForExtraction($property, $context);

                $this->extractionMethodsCache[$objectClass][$method] = $property;
            }
        }

        // Pass 2: actually extract data
        foreach ($this->extractionMethodsCache[$objectClass] as $method => $property) {
            $result[$property] = $this->extractValue($property, $object->$method(), $context);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate(array $data, $object)
    {
        $objectClass = get_class($object);
        $context     = new HydrationContext($data, $object);

        foreach ($data as $property => $value) {
            $propertyFqn = $objectClass . '::$' . $property;

            if (!isset($this->hydrationMethodsCache[$propertyFqn])) {
                $property = $this->namingStrategy->getNameForHydration($property, $context);
                $method   = 'set' . $property; // PHP is case insensitive for call methods, no
                                               // need to uppercase first character

                $this->hydrationMethodsCache[$propertyFqn] = is_callable([$object, $method])
                    ? $method
                    : false;
            }

            if ($this->hydrationMethodsCache[$propertyFqn]) {
                $object->{$this->hydrationMethodsCache[$propertyFqn]}($this->hydrateValue($property, $value, $context));
            }
        }

        return $object;
    }
}
