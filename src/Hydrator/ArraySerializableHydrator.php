<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Hydrator;

/**
 * This hydrator uses the getArrayCopy() method to extract an object
 * and exchangeArray() or populate() method to hydrate the object
 */
final class ArraySerializableHydrator extends AbstractHydrator
{
    /**
     * {@inheritDoc}
     */
    public function extract($object)
    {
        if (!is_callable([$object, 'getArrayCopy'])) {
            throw new Exception\BadMethodCallException(sprintf(
                '%s expects the provided object to implement getArrayCopy()',
                __METHOD__
            ));
        }

        $data   = $object->getArrayCopy();
        $result = [];

        $context         = clone $this->extractionContext; // Performance trick, do not try to instantiate
        $context->object = $object;

        foreach ($data as $property => $value) {
            $property          = $this->namingStrategy->getNameForExtraction($property, $context);
            $result[$property] = $this->extractValue($property, $value, $context);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate(array $data, $object)
    {
        if (!(is_callable([$object, 'exchangeArray']) || is_callable([$object, 'populate']))) {
            throw new Exception\BadMethodCallException(sprintf(
                '%s expects the provided object to implement exchangeArray() or populate()',
                __METHOD__
            ));
        }

        $context         = clone $this->hydrationContext; // Performance trick, do not try to instantiate
        $context->object = $object;
        $context->data   = $data;

        $replacement = [];

        foreach ($data as $property => &$value) {
            $property               = $this->namingStrategy->getNameForHydration($property, $context);
            $replacement[$property] = $this->hydrateValue($property, $value, $context);
        }

        if (is_callable([$object, 'exchangeArray'])) {
            $object->exchangeArray($replacement);
        } else {
            $object->populate($replacement);
        }

        return $object;
    }
}
