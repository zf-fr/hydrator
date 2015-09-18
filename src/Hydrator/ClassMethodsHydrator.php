<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Hydrator;

use Hydrator\Filter\CompositeFilter;
use Hydrator\Filter\FilterChain;
use Hydrator\Filter\FilterInterface;
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
final class ClassMethodsHydrator extends AbstractHydrator
{
    /**
     * @var FilterChain
     */
    private $filterChain;

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
     * @param FilterInterface|null $optionalFilter
     */
    public function __construct(FilterInterface $optionalFilter = null)
    {
        parent::__construct();

        $this->filterChain = new FilterChain();

        $this->filterChain->andFilter(new CompositeFilter(
            [new GetFilter(), new IsFilter(), new HasFilter()],
            CompositeFilter::TYPE_OR
        ));

        $this->filterChain->andFilter(new OptionalParametersFilter());

        if ($optionalFilter) {
            $this->filterChain->andFilter($optionalFilter);
        }
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

        $result = [];

        $context         = clone $this->extractionContext; // Performance trick, do not try to instantiate
        $context->object = $object;

        // Pass 1: finding out which properties can be extracted, with which methods (populate hydration cache)
        if (!isset($this->extractionMethodsCache[$objectClass])) {
            $this->extractionMethodsCache[$objectClass] = [];

            foreach ($methods as $method) {
                if (!$this->filterChain->accept($method, $context)) {
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

        $context         = clone $this->hydrationContext; // Performance trick, do not try to instantiate
        $context->object = $object;
        $context->data   = $data;

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
